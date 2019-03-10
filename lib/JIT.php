<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCfg\Operand;
use PHPCfg\Op;
use PHPCfg\Block as CfgBlock;
use PHPTypes\Type;
use PHPCompiler\JIT\Context;
use PHPCompiler\JIT\Variable;


class JIT {

    const COMPARE_MAP = [
        OpCode::TYPE_SMALLER_OR_EQUAL => \GCC_JIT_COMPARISON_LE,
        OpCode::TYPE_GREATER_OR_EQUAL => \GCC_JIT_COMPARISON_GE,
        OpCode::TYPE_GREATER => \GCC_JIT_COMPARISON_GT,
        OpCode::TYPE_SMALLER => \GCC_JIT_COMPARISON_LT,
        OpCode::TYPE_IDENTICAL => \GCC_JIT_COMPARISON_EQ,
    ];

    const BINARYOP_MAP = [
        OpCode::TYPE_MINUS => \GCC_JIT_BINARY_OP_MINUS,
        OpCode::TYPE_PLUS => \GCC_JIT_BINARY_OP_PLUS,
        OpCode::TYPE_MUL => \GCC_JIT_BINARY_OP_MULT,
        OpCode::TYPE_DIV => \GCC_JIT_BINARY_OP_DIVIDE,
        OpCode::TYPE_MODULO => \GCC_JIT_BINARY_OP_MODULO,
        OpCode::TYPE_BITWISE_AND => \GCC_JIT_BINARY_OP_BITWISE_AND,
        OpCode::TYPE_BITWISE_OR => \GCC_JIT_BINARY_OP_BITWISE_OR,
        OpCode::TYPE_BITWISE_XOR => \GCC_JIT_BINARY_OP_BITWISE_XOR,
    ];

    const UNARYOP_MAP = [
        OpCode::TYPE_UNARY_MINUS => \GCC_JIT_UNARY_OP_MINUS,
        OpCode::TYPE_BITWISE_NOT => \GCC_JIT_UNARY_OP_BITWISE_NEGATE,
        OpCode::TYPE_BOOLEAN_NOT => \GCC_JIT_UNARY_OP_LOGICAL_NEGATE
    ];

    private static int $functionNumber = 0;
    private static int $blockNumber = 0;

    public int $optimizationLevel = 3;


    private array $stringConstant = [];
    private array $intConstant = [];
    private array $builtIns = [];

    private array $queue = [];

    private Context $context;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function compile(Block $block): \gcc_jit_function_ptr {
        $return = $this->compileBlock($block);
        $this->runQueue();
        return $return;
    }

    private function runQueue(): void {
        while (!empty($this->queue)) {
            $run = array_shift($this->queue);
            $this->compileBlockInternal($run[0], $run[1], ...$run[2]);
        }
    }

    private function compileBlock(Block $block, ?string $funcName = null): \gcc_jit_function_ptr {
        $internalName = "internal_" . (++self::$functionNumber);
        
        $args = [];
        $argVars = [];
        if (!is_null($block->func)) {
            $callbackType = '';
            if ($block->func->returnType instanceof Op\Type\Literal) {
                switch ($block->func->returnType->name) {
                    case 'void':
                        $callbackType = 'void';
                        break;
                    case 'int':
                        $callbackType = 'long long';
                        break;
                    case 'string':
                        $callbackType = '__string__*';
                        break;
                    default:
                        throw new \LogicException("Non-void return types not supported yet");
                }
            } else {
                throw new \LogicException("Non-typed functions not implemented yet");
            }
            $returnType = $this->context->getTypeFromString($callbackType);
            $callbackType .= '(*)(';
            $callbackSep = '';
            foreach ($block->func->params as $idx => $param) {
                if (empty($param->result->usages)) {
                    // only compile for param
                    assert($param->declaredType instanceof Op\Type\Literal);
                    $rawType = Type::fromDecl($param->declaredType->name);
                } else {
                    $rawType = $param->result->type;
                }
                $type = $this->context->getTypeFromType($rawType);
                $callbackType .= $callbackSep . $this->context->getStringFromType($type);
                $callbackSep = ', ';
                $args[] = $arg = \gcc_jit_context_new_param($this->context->context, $this->context->location(), $type, 'param_' . $idx);
                $argVars[] = new Variable(
                    $this->context,
                    Variable::getTypeFromType($rawType),
                    Variable::KIND_VARIABLE,
                    $arg->asRValue(),
                    $arg->asLValue()
                );
            }
            $callbackType .= ')';
        } else {
            $callbackType = 'void(*)()';
            $returnType = $this->context->getTypeFromString('void');
        }

        $func = \gcc_jit_context_new_function(
            $this->context->context, 
            NULL,
            \GCC_JIT_FUNCTION_EXPORTED,
            $returnType,
            $internalName,
            count($args), 
            \gcc_jit_param_ptr_ptr::fromArray(...$args),
            0
        );
        if (!is_null($funcName)) {
            $this->context->functions[strtolower($funcName)] = new JIT\Func\Trampolined(
                $this->context,
                $funcName,
                $func,
                $returnType,
                ...$args
            );
        }

        $this->queue[] = [$func, $block, $argVars];
        if ($callbackType === 'void(*)()') {
            $this->context->addExport($internalName, $callbackType, $block);
        }
        return $func;
    }

    
    private function compileBlockInternal(
        \gcc_jit_function_ptr $func,
        Block $block,
        Variable ...$args
    ): \gcc_jit_block_ptr {
        if ($this->context->scope->blockStorage->contains($block)) {
            return $this->context->scope->blockStorage[$block];
        }
        self::$blockNumber++;
        $gccBlock = \gcc_jit_function_new_block($func, 'block_' . self::$blockNumber);
        $this->context->scope->blockStorage[$block] = $gccBlock;
        // Handle hoisted variables
        foreach ($block->orig->hoistedOperands as $operand) {
            $var = $this->context->makeVariableFromOp($func, $gccBlock, $block, $operand);
        }

        for ($i = 0, $length = count($block->opCodes); $i < $length; $i++) {
            $op = $block->opCodes[$i];
            switch ($op->type) {
                case OpCode::TYPE_ARG_RECV:
                    $this->context->helper->assignOperand($gccBlock, $block->getOperand($op->arg1), $args[$op->arg2]);
                    break;
                case OpCode::TYPE_ASSIGN:
                    $value = $this->context->getVariableFromOp($block->getOperand($op->arg3));
                    $this->context->helper->assignOperand($gccBlock, $block->getOperand($op->arg2), $value);
                    $this->context->helper->assignOperand($gccBlock, $block->getOperand($op->arg1), $value);
                    break;  
                case OpCode::TYPE_ARRAY_DIM_FETCH:
                    $value = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                    $dim = $this->context->getVariableFromOp($block->getOperand($op->arg3));
                    if ($value->type === Variable::TYPE_STRING) {
                        $this->context->helper->assignOperand(
                            $gccBlock, 
                            $block->getOperand($op->arg1),
                            $value->dimFetch($dim)
                        );
                    } else {
                        throw new \LogicException("Illegal dim fetch");
                    }
                    break;
                case OpCode::TYPE_CONCAT:
                    $result = $this->context->getVariableFromOp($block->getOperand($op->arg1));
                    $left = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                    $right = $this->context->getVariableFromOp($block->getOperand($op->arg3));
                    $this->context->type->string->concat($gccBlock, $result, $left, $right);
                    break;
                case OpCode::TYPE_CAST_BOOL:
                    $value = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                    $this->context->helper->assignOperand($gccBlock, $block->getOperand($op->arg1), $value->castTo(Variable::TYPE_NATIVE_BOOL));
                    break;
                case OpCode::TYPE_ECHO:
                case OpCode::TYPE_PRINT:
                    $argOffset = $op->type === OpCode::TYPE_ECHO ? $op->arg1 : $op->arg2;
                    $arg = $this->context->getVariableFromOp($block->getOperand($argOffset));
                    switch ($arg->type) {
                        case Variable::TYPE_STRING:
                            $this->context->helper->eval(
                                $gccBlock,
                                $this->context->helper->call(
                                    'printf',
                                    $this->context->constantFromString('%.*s'),
                                    $this->context->type->string->size($arg),
                                    $this->context->type->string->value($arg)
                                )
                            );
                            break;
                        case Variable::TYPE_NATIVE_LONG:
                            $this->context->helper->eval(
                                $gccBlock,
                                $this->context->helper->call(
                                    'printf',
                                    $this->context->constantFromString('%lld'),
                                    $arg->rvalue
                                )
                            );
                            break;
                        case Variable::TYPE_NATIVE_DOUBLE:
                            $this->context->helper->eval(
                                $gccBlock,
                                $this->context->helper->call(
                                    'printf',
                                    $this->context->constantFromString('%G'),
                                    $arg->rvalue
                                )
                            );
                            break;
                        default: 
                            throw new \LogicException("Echo for type $arg->type not implemented");
                    }
                    if ($op->type === OpCode::TYPE_PRINT) {
                        $this->context->helper->assignOperand(
                            $gccBlock, 
                            $block->getOperand($op->arg1),
                            new Variable($this->context, Variable::TYPE_NATIVE_LONG, Variable::KIND_VALUE, $this->context->constantFromInteger(1), null)
                        );
                    }
                    break;
                case OpCode::TYPE_MUL:
                case OpCode::TYPE_PLUS:
                case OpCode::TYPE_MINUS:
                case OpCode::TYPE_DIV:
                case OpCode::TYPE_MODULO:
                case OpCode::TYPE_BITWISE_AND:
                case OpCode::TYPE_BITWISE_OR:
                case OpCode::TYPE_BITWISE_XOR:
                    $result = $block->getOperand($op->arg1);
                    $this->context->makeVariableFromRValueOp(
                        $this->context->helper->numericBinaryOp(
                            self::BINARYOP_MAP[$op->type], 
                            $result, 
                            $this->context->getVariableFromOp($block->getOperand($op->arg2)), 
                            $this->context->getVariableFromOp($block->getOperand($op->arg3))
                        ),
                        $result
                    );
                    break;
                case OpCode::TYPE_GREATER_OR_EQUAL:
                case OpCode::TYPE_SMALLER_OR_EQUAL:
                case OpCode::TYPE_GREATER:
                case OpCode::TYPE_SMALLER:
                case OpCode::TYPE_IDENTICAL:
                    $result = $block->getOperand($op->arg1);
                    $this->context->makeVariableFromRValueOp(
                        $this->context->helper->compareOp(
                            self::COMPARE_MAP[$op->type], 
                            $result, 
                            $this->context->getVariableFromOp($block->getOperand($op->arg2)), 
                            $this->context->getVariableFromOp($block->getOperand($op->arg3))
                        ),
                        $result
                    );
                    break;
                case OpCode::TYPE_EQUAL:
                    $result = $block->getOperand($op->arg1);
                    $left = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                    $right = $this->context->getVariableFromOp($block->getOperand($op->arg3));
                    if ($left->type === $right->type) {
                        $this->context->makeVariableFromRValueOp(
                            $this->context->helper->compareOp(
                                \GCC_JIT_COMPARISON_EQ, 
                                $result, 
                                $left,
                                $right
                            ),
                            $result
                        );
                    } else {
                        throw new \LogicException('Equals between disparate types not implemented yet');
                    }
                    break;
                case OpCode::TYPE_UNARY_MINUS:
                    $result = $block->getOperand($op->arg1);
                    $this->context->makeVariableFromRValueOp(
                        $this->context->helper->numericUnaryOp(
                            self::UNARYOP_MAP[$op->type], 
                            $result, 
                            $this->context->getVariableFromOp($block->getOperand($op->arg2))
                        ),
                        $result
                    );
                    break;
                case OpCode::TYPE_CASE:
                    // the first case we arrive to. all the rest of the cases will have the same type, so we're good:
                    $cases = [];
                    $default = null;
                    $condition = $this->context->getVariableFromOp($block->getOperand($op->arg1));
                    while ($i < $length) {
                        // Note, the first iteration will capture the current op, this is intentional
                        if ($block->opCodes[$i]->type === OpCode::TYPE_CASE) {
                            $caseCondition = $this->context->getVariableFromOp($block->getOperand($block->opCodes[$i]->arg2))->rvalue;
                            $cases[] = \gcc_jit_context_new_case(
                                $this->context->context,
                                $caseCondition,
                                $caseCondition,
                                $this->compileBlockInternal($func, $block->opCodes[$i]->block1, ...$args)
                            );
                        } elseif ($block->opCodes[$i]->type === OpCode::TYPE_JUMP) {
                            if (!is_null($default)) {
                                throw new \LogicException('More than one default to switch found. Really weird');
                            }
                            $default = $this->compileBlockInternal($func, $block->opCodes[$i]->block1, ...$args);
                        } else {
                            throw new \LogicException('Mixed instruction inside of switch statement found: ' . $block->opCodes[$i]->getType());
                        }
                        $i++;
                    }
                    if (is_null($default)) {
                        throw new \LogicException("Switch must have at least a default block: compile error");
                    }
                    \gcc_jit_block_end_with_switch(
                        $gccBlock,
                        $this->context->location(),
                        $condition->rvalue,
                        $default,
                        count($cases),
                        \gcc_jit_case_ptr_ptr::fromArray(...$cases)
                    );
                    return $gccBlock;
                case OpCode::TYPE_JUMP:
                    $newBlock = $this->compileBlockInternal($func, $op->block1, ...$args);
                    $this->context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_jump(
                        $gccBlock,
                        $this->context->location(),
                        $newBlock
                    );
                    return $gccBlock;
                case OpCode::TYPE_JUMPIF:
                    $if = $this->compileBlockInternal($func, $op->block1, ...$args);
                    $else = $this->compileBlockInternal($func, $op->block2, ...$args);
                    $condition = $this->context->castToBool(
                        $this->context->getVariableFromOp($block->getOperand($op->arg1))->rvalue
                    );

                    $this->context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_conditional(
                        $gccBlock,
                        $this->context->location(),
                        $condition,
                        $if,
                        $else
                    );
                    return $gccBlock;
                case OpCode::TYPE_RETURN_VOID:
                    $this->context->freeDeadVariables($func, $gccBlock, $block);
                    goto void_return;
                    break;
                case OpCode::TYPE_RETURN:
                    $return = $this->context->getVariableFromOp($block->getOperand($op->arg1));
                    $return->addref($gccBlock);
                    $this->context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_return(
                        $gccBlock,
                        $this->context->location(),
                        $return->rvalue
                    );
                    return $gccBlock;
                case OpCode::TYPE_FUNCDEF:
                    $nameOp = $block->getOperand($op->arg1);
                    assert($nameOp instanceof Operand\Literal);
                    $this->compileBlock($op->block1, $nameOp->value);
                    break;
                case OpCode::TYPE_FUNCCALL_INIT:
                    $nameOp = $block->getOperand($op->arg1);
                    if (!$nameOp instanceof Operand\Literal) {
                        throw new \LogicException("Variable function calls not yet supported");
                    }
                    $lcname = strtolower($nameOp->value);
                    if (!isset($this->context->functions[$lcname])) {
                        throw new \RuntimeException("Call to undefined function $lcname");
                    }
                    $this->context->scope->toCall = $this->context->functions[$lcname];
                    $this->context->scope->args = [];
                    break;
                case OpCode::TYPE_ARG_SEND:
                    $this->context->scope->args[] = $this->context->getVariableFromOp($block->getOperand($op->arg1))->rvalue;
                    break;
                case OpCode::TYPE_FUNCCALL_EXEC_NORETURN:
                    if (is_null($this->context->scope->toCall)) {
                        // short circuit
                        break;
                    }
                    $this->context->helper->eval($gccBlock, $this->context->scope->toCall->call(...$this->context->scope->args));
                    break;
                case OpCode::TYPE_FUNCCALL_EXEC_RETURN:
                    $this->context->helper->assign(
                        $gccBlock,
                        $this->context->getVariableFromOp($block->getOperand($op->arg1))->lvalue,
                        $this->context->scope->toCall->call(...$this->context->scope->args)
                    );
                    break;
                case OpCode::TYPE_DECLARE_CLASS:
                    $this->context->pushScope();
                    $this->context->scope->classId = $this->context->type->object->declareClass($block->getOperand($op->arg1));
                    $this->compileClass($op->block1, $this->context->scope->classId);
                    $this->context->popScope();
                    break;
                case OpCode::TYPE_NEW:
                    $class = $this->context->type->object->lookupOperand($block->getOperand($op->arg2));
                    $this->context->helper->assign(
                        $gccBlock,
                        $this->context->getVariableFromOp($block->getOperand($op->arg1))->lvalue,
                        $this->context->type->object->allocate($class)
                    );
                    $this->context->scope->toCall = null;
                    $this->context->scope->args = [];
                    break;
                case OpCode::TYPE_PROPERTY_FETCH:
                    $result = $block->getOperand($op->arg1);
                    $obj = $block->getOperand($op->arg2);
                    $name = $block->getOperand($op->arg3);
                    assert($name instanceof Operand\Literal);
                    assert($obj->type->type === Type::TYPE_OBJECT);
                    $this->context->scope->variables[$result] = $this->context->type->object->propertyFetch(
                        $this->context->getVariableFromOp($obj)->rvalue,
                        $obj->type->userType,
                        $name->value
                    );
                    break;
                default:
                    throw new \LogicException("Unknown JIT opcode: ". $op->getType());
            }
        }
void_return:
        \gcc_jit_block_end_with_void_return($gccBlock, null);
        return $gccBlock;
    }

    private function compileClass(?Block $block, int $classId) {
        if ($block === null) {
            return;
        }
        foreach ($block->opCodes as $op) {
            switch ($op->type) {
                case OpCode::TYPE_DECLARE_PROPERTY:
                    $name = $block->getOperand($op->arg1);
                    assert($name instanceof Operand\Literal);
                    assert(is_null($op->arg2)); // no defaults for now
                    $type = Variable::getTypeFromType($block->getOperand($op->arg3)->type);
                    $this->context->type->object->defineProperty($classId, $name->value, $type);
                    break;
                default:
                    var_dump($op);
                    throw new \LogicException('Other class body types are not jittable for now');
            }
            
        }
    }

    private static function compileArg(Operand $op): \gcc_jit_param_ptr {
        throw new \LogicException("Block args not implemented yet");
    }

}