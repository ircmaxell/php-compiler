<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCfg\Func;
use PHPCfg\Op;
use PHPCfg\Block as CfgBlock;
use PHPCfg\Operand;
use PHPCfg\Script;
use PHPTypes\Type;
use PHPCompiler\VM\Variable;

class Compiler {

    protected ?\SplObjectStorage $seen;
    protected ?\SplObjectStorage $funcs;

    public function compile(Script $script): ?Block {
        $this->seen = new \SplObjectStorage;

        $main = $this->compileCfgBlock($script->main->cfg);

        $this->seen = null;
        return $main;
    }

    protected function compileCfgBlock(CfgBlock $block, array $params = []): Block {
        if (!$this->seen->contains($block)) {
            $this->seen[$block] = $new = new Block($block);
            $paramIdx = 0;
            foreach ($params as $param) {
                $new->addOpCode($this->compileParam($param, $new, $paramIdx++));                
            }
            $this->compileBlock($new);
        }
        return $this->seen[$block];
    }

    protected function compileBlock(Block $block) {
        $this->compileOps($block->orig->children, $block);
    }

    protected function compileOps(array $ops, Block $block): void {
        // First hoist functions and class definitions
        foreach ($ops as $child) {
            switch (get_class($child)) {
                case Op\Stmt\Function_::class:
                    $block->addOpCode($this->compileFunction($child, $block));
                    break;
                case Op\Stmt\Class_::class:
                case Op\Stmt\Interface_::class:
                case Op\Stmt\Trait_::class:
                    $block->addOpCode($this->compileClassLike($child, $block));
                    break;
            }
        }
        foreach ($ops as $child) {
            switch (get_class($child)) {
                case Op\Stmt\Function_::class:
                case Op\Stmt\Class_::class:
                case Op\Stmt\Interface_::class:
                case Op\Stmt\Trait_::class:
                    break;
                default:
                    $this->compileOp($child, $block);
            }
        }
    }

    protected function compileClassLike(Op\Stmt\ClassLike $class, Block $block): OpCode {
        $type = 0;
        if ($class instanceof Op\Stmt\Class_) {
            $type = OpCode::TYPE_DECLARE_CLASS;
            assert(null === $class->extends);
            assert(empty($class->implements));
        } else {
            throw new \LogicException('Unsupported class type: ' . get_class($class));
        }
        $return = new OpCode(
            $type,
            $this->compileOperand($class->name, $block, true)
        );
        $return->block1 = $this->compileClassBody($class->stmts, $type);
        return $return;
    }

    protected function compileClassBody(CfgBlock $block, int $type): Block {
        $result = new Block($block);
        foreach ($block->children as $child) {
            switch (get_class($child)) {
                case Op\Stmt\Property::class:
                    if ($type !== OpCode::TYPE_DECLARE_CLASS) {
                        throw new \LogicException('Properties are only supported on classes for now');
                    }
                    if (!is_null($child->defaultBlock)) {
                        $this->compileOps($child->defaultBlock, $result);
                    }
                    $result->addOpCode(new OpCode(
                        OpCode::TYPE_DECLARE_PROPERTY,
                        $this->compileOperand($child->name, $result, true),
                        is_null($child->defaultVar) ? null : $this->compileOperand($child->defaultVar, $result, true),
                        $this->compileTypeConstrainedVariable($result, $child->type)
                    ));
                    break;
                default:
                    throw new \LogicException('Unsupported class body element: ' . get_class($child));
            }
        }
        return $result;
    }

    protected function compileTypeConstrainedVariable(Block $block, Type $type): int {
        $var = new Variable(Variable::TYPE_UNKNOWN);
        $operand = new Operand\Temporary;
        $operand->type = $type;
        $return = $block->registerConstant($operand, $var);
        $mappedType = Variable::mapFromType($type);
        if ($mappedType === Variable::TYPE_UNKNOWN) {
            // Mixed
            return $return;
        } elseif ($mappedType === Variable::TYPE_OBJECT) {
            $var->classConstraint = $type->userType;
        }
        $var->typeConstraint = $mappedType;
        return $return;
    }


    protected function compileParam(Op\Expr\Param $param, Block $block, int $paramIdx): OpCode {
        assert(false === $param->byRef);
        assert(false === $param->variadic);
        assert(null === $param->defaultBlock);
        return new OpCode(
            OpCode::TYPE_ARG_RECV,
            $this->compileOperand($param->result, $block, false),
            $paramIdx
        );
    }

    protected function compileFunction(Op\Stmt\Function_ $function, Block $block): OpCode {
        $funcBlock = $this->compileCfgBlock($function->func->cfg, $function->func->params);
        $funcBlock->func = $function->func;
        $operand = new Operand\Literal($function->func->name);
        $operand->type = Type::string();
        $return = new OpCode(
            OpCode::TYPE_FUNCDEF,
            $this->compileOperand($operand, $block, true)
        );
        $return->block1 = $funcBlock;
        return $return;
    }

    protected function compileOp(Op $op, Block $block) {
        if ($op instanceof Op\Expr\ConcatList) {
            $total = count($op->list);
            assert($total >= 2);
            $pointer = 2;

            $return = $this->compileOperand($op->result, $block, false);
            $block->addOpCode(new OpCode(
                OpCode::TYPE_CONCAT,
                $return,
                $this->compileOperand($op->list[0], $block, true),
                $this->compileOperand($op->list[1], $block, true)
            ));
            while ($pointer < $total) {
                $right = $this->compileOperand($op->list[$pointer++], $block, true);
                $block->addOpCode(new OpCode(
                    OpCode::TYPE_CONCAT,
                    $return,
                    $return,
                    $right
                ));
            }
        } elseif ($op instanceof Op\Expr) {
            $block->addOpCode(...$this->compileExpr($op, $block));
        } elseif ($op instanceof Op\Stmt) {
            $this->compileStmt($op, $block);
        } elseif ($op instanceof Op\Terminal) {
            $block->addOpCode($this->compileTerminal($op, $block));
        } else {
            throw new \LogicException("Unknown Op Type: " . $op->getType());
        }
    }

    protected function compileStmt(Op\Stmt $stmt, Block $block) {
        if ($stmt instanceof Op\Stmt\Jump) {
            $op = new OpCode(OpCode::TYPE_JUMP);
            $op->block1 = $this->compileCfgBlock($stmt->target);
            $op->block1->parents[] = $block;
            $block->addOpCode($op);
        } elseif ($stmt instanceof Op\Stmt\JumpIf) {
            $op = new OpCode(OpCode::TYPE_JUMPIF, $this->compileOperand($stmt->cond, $block, true));
            $op->block1 = $this->compileCfgBlock($stmt->if);
            $op->block2 = $this->compileCfgBlock($stmt->else);
            $op->block1->parents[] = $block;
            $op->block2->parents[] = $block;
            $block->addOpCode($op);
        } else {
            throw new \LogicException("Unknown Stmt Type: " . $stmt->getType());
        }
    }

    protected function getOpCodeTypeFromBinaryOp(Op\Expr\BinaryOp $expr): int {
        if ($expr instanceof Op\Expr\BinaryOp\Concat) {
            return OpCode::TYPE_CONCAT;
        } elseif ($expr instanceof Op\Expr\BinaryOp\Plus) {
            return OpCode::TYPE_PLUS;
        } elseif ($expr instanceof Op\Expr\BinaryOp\Smaller) {
            return OpCode::TYPE_SMALLER;
        } elseif ($expr instanceof Op\Expr\BinaryOp\Greater) {
            return OpCode::TYPE_GREATER;
        } elseif ($expr instanceof Op\Expr\BinaryOp\Identical) {
            return OpCode::TYPE_IDENTICAL;
        } elseif ($expr instanceof Op\Expr\BinaryOp\Minus) {
            return OpCode::TYPE_MINUS;
        } elseif ($expr instanceof Op\Expr\BinaryOp\Mul) {
            return OpCode::TYPE_MUL;
        } elseif ($expr instanceof Op\Expr\BinaryOp\Div) {
            return OpCode::TYPE_DIV;
        }
        throw new \LogicException("Unknown BinaryOp Type: " . $expr->getType());
    }

    protected function compileExpr(Op\Expr $expr, Block $block): array {
        if ($expr instanceof Op\Expr\BinaryOp) {
            return [new OpCode(
                $this->getOpCodeTypeFromBinaryOp($expr),
                $this->compileOperand($expr->result, $block, false),
                $this->compileOperand($expr->left, $block, true),
                $this->compileOperand($expr->right, $block, true),
            )];
        }
        switch (get_class($expr)) {
            case Op\Expr\Assign::class:
                return [new OpCode(
                    OpCode::TYPE_ASSIGN,
                    $this->compileOperand($expr->result, $block, false),   
                    $this->compileOperand($expr->var, $block, false),
                    $this->compileOperand($expr->expr, $block, true) 
                )];
            case Op\Expr\ConstFetch::class:
                $nsName = null;
                if (!is_null($expr->nsName)) {
                    $nsName = $this->compileOperand($expr->nsName, $block, true);
                }
                return [new OpCode(
                    OpCode::TYPE_CONST_FETCH,
                    $this->compileOperand($expr->result, $block, false),
                    $this->compileOperand($expr->name, $block, true),
                    $nsName
                )];
            case Op\Expr\FuncCall::class:
                $return = [
                    new OpCode(
                        OpCode::TYPE_FUNCCALL_INIT,
                        $this->compileOperand($expr->name, $block, true)
                    )
                ];
                foreach ($expr->args as $arg) {
                    $return[] = new OpCode(
                        OpCode::TYPE_ARG_SEND,
                        $this->compileOperand($arg, $block, true)
                    );
                }
                if (!empty($expr->result->usages)) {
                    $return[] = new OpCode(
                        OpCode::TYPE_FUNCCALL_EXEC_RETURN,
                        $this->compileOperand($expr->result, $block, false)
                    );
                } else {
                    $return[] = new OpCode(
                        OpCode::TYPE_FUNCCALL_EXEC_NORETURN,
                    );
                }
                return $return;
            case Op\Expr\New_::class:
                $return = [
                    new OpCode(
                        OpCode::TYPE_NEW,
                        $this->compileOperand($expr->result, $block, false),
                        $this->compileOperand($expr->class, $block, true),
                    )
                ];
                foreach ($expr->args as $arg) {
                    $return[] = new OpCode(
                        OpCode::TYPE_ARG_SEND,
                        $this->compileOperand($arg, $block, true)
                    );
                }
                $return[] = new OpCode(
                    OpCode::TYPE_FUNCCALL_EXEC_NORETURN
                );
                return $return;
            case Op\Expr\PropertyFetch::class:
                return [new OpCode(
                    OpCode::TYPE_PROPERTY_FETCH,
                    $this->compileOperand($expr->result, $block, false),
                    $this->compileOperand($expr->var, $block, true),
                    $this->compileOperand($expr->name, $block, true)
                )];
        }
        throw new \LogicException("Unsupported expression: " . $expr->getType());
    }

    protected function compileOperand(Operand $operand, Block $block, bool $isRead): int {
        if ($operand instanceof Operand\Literal) {
            assert($isRead === true);
            $return = new Variable(Variable::mapFromType($operand->type));
            switch (Variable::mapFromType($operand->type)) {
                case Variable::TYPE_STRING:
                    $return->string($operand->value);
                    break;
                case Variable::TYPE_INTEGER:
                    $return->int($operand->value);
                    break;
                case Variable::TYPE_FLOAT:
                    $return->float($operand->value);
                    break;
                default:
                    throw new \LogicException("Unknown Literal Operand Type: " . $operand->type);
            }
            return $block->registerConstant($operand, $return);
        } elseif ($operand instanceof Operand\Temporary) {
            return $block->getVarSlot($operand, $isRead);
        }
        throw new \LogicException("Unknown Operand Type: " . $operand->getType());
    }

    protected function compileTerminal(Op\Terminal $terminal, Block $block): OpCode {
        switch ($terminal->getType()) {
            case 'Terminal_Echo':
                $var = $this->compileOperand($terminal->expr, $block, true);
                return new OpCode(
                    OpCode::TYPE_ECHO,
                    $var
                );
            case 'Terminal_Return':
                if (is_null($terminal->expr)) {
                    return new OpCode(
                        OpCode::TYPE_RETURN_VOID
                    );    
                }
                return new OpCode(
                    OpCode::TYPE_RETURN,
                    $this->compileOperand($terminal->expr, $block, true)
                );
            default:
                throw new \LogicException("Unknown Terminal Type: " . $terminal->getType());
        }
    }

}