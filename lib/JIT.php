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
    private static int $functionNumber = 0;

    public static int $optimizationLevel = 3;


    private static array $stringConstant = [];
    private static array $intConstant = [];
    private static array $builtIns = [];

    const COMPARE_MAP = [
        OpCode::TYPE_GREATER => \GCC_JIT_COMPARISON_GT,
        OpCode::TYPE_SMALLER => \GCC_JIT_COMPARISON_LT,
        OpCode::TYPE_IDENTICAL => \GCC_JIT_COMPARISON_EQ,
    ];

    const BINARYOP_MAP = [
        OpCode::TYPE_MINUS => \GCC_JIT_BINARY_OP_MINUS,
        OpCode::TYPE_PLUS => \GCC_JIT_BINARY_OP_PLUS,
        OpCode::TYPE_MUL => \GCC_JIT_BINARY_OP_MULT,
        OpCode::TYPE_DIV => \GCC_JIT_BINARY_OP_DIVIDE,
    ];

    public static function compile(Block $block, int $loadType, ?string $debugFile = null): Context {
        $context = new Context($loadType);
        if (!is_null($debugFile)) {
            $context->setDebugFile($debugFile);
        }
        $context->setMain(self::compileBlock($context, $block));
        return $context;
    }


    private static function compileBlock(Context $context, Block $block, ?string $funcName = null): \gcc_jit_function_ptr {
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
            $returnType = $context->getTypeFromString($callbackType);
            $callbackType .= '(*)(';
            $callbackSep = '';
            foreach ($block->func->params as $idx => $param) {
                $type = $context->getTypeFromType($param->result->type);
                $callbackType .= $callbackSep . $context->getStringFromType($type);
                $callbackSep = ', ';
                $args[] = $arg = \gcc_jit_context_new_param($context->context, $context->location(), $type, 'param_' . $idx);
                $argVars[] = new Variable(
                    $context,
                    Variable::getTypeFromType($param->result->type),
                    Variable::KIND_VARIABLE,
                    $arg->asRValue(),
                    $arg->asLValue()
                );
            }
            $callbackType .= ')';
        } else {
            $callbackType = 'void(*)()';
            $returnType = $context->getTypeFromString('void');
        }

        $func = \gcc_jit_context_new_function(
            $context->context, 
            NULL,
            \GCC_JIT_FUNCTION_EXPORTED,
            $returnType,
            $internalName,
            count($args), 
            \gcc_jit_param_ptr_ptr::fromArray(...$args),
            0
        );
        if (!is_null($funcName)) {
            $context->functions[strtolower($funcName)] = $func;
        }

        self::compileBlockInternal($context, $func, $block, ...$argVars);
        if ($callbackType === 'void(*)()') {
            $context->addExport($internalName, $callbackType, $block);
        }
        return $func;
    }

    private static int $blockNumber = 0;
    private function compileBlockInternal(
        Context $context, 
        \gcc_jit_function_ptr $func,
        Block $block,
        Variable ...$args
    ): \gcc_jit_block_ptr {
        if ($context->scope->blockStorage->contains($block)) {
            return $context->scope->blockStorage[$block];
        }
        $context->scope->blockNumber++;
        $gccBlock = \gcc_jit_function_new_block($func, 'block_' . self::$blockNumber);
        $context->scope->blockStorage[$block] = $gccBlock;
        // Handle hoisted variables
        foreach ($block->orig->hoistedOperands as $operand) {
            $var = $context->makeVariableFromOp($func, $gccBlock, $block, $operand);
        }

        foreach ($block->opCodes as $op) {
            switch ($op->type) {
                case OpCode::TYPE_ARG_RECV:
                    $context->helper->assignOperand($gccBlock, $block->getOperand($op->arg1), $args[$op->arg2]);
                    break;
                case OpCode::TYPE_ASSIGN:
                    $value = $context->getVariableFromOp($block->getOperand($op->arg3));
                    $context->helper->assignOperand($gccBlock, $block->getOperand($op->arg2), $value);
                    $context->helper->assignOperand($gccBlock, $block->getOperand($op->arg1), $value);
                    break;  
                case OpCode::TYPE_CONCAT:
                    $result = $context->getVariableFromOp($block->getOperand($op->arg1));
                    $left = $context->getVariableFromOp($block->getOperand($op->arg2));
                    $right = $context->getVariableFromOp($block->getOperand($op->arg3));
                    $context->type->string->concat($gccBlock, $result, $left, $right);
                    break;
                case OpCode::TYPE_ECHO:
                    $arg = $context->getVariableFromOp($block->getOperand($op->arg1));
                    switch ($arg->type) {
                        case Variable::TYPE_STRING:
                            $context->helper->eval(
                                $gccBlock,
                                $context->helper->call(
                                    'printf',
                                    $context->constantFromString('%.*s'),
                                    $context->type->string->size($arg),
                                    $context->type->string->value($arg)
                                )
                            );
                            break;
                        case Variable::TYPE_NATIVE_LONG:
                            $context->helper->eval(
                                $gccBlock,
                                $context->helper->call(
                                    'printf',
                                    $context->constantFromString('%lld'),
                                    $arg->rvalue
                                )
                            );
                            break;
                        default: 
                            throw new \LogicException("Echo for type $arg->type not implemented");
                    }
                    
                    break;
                case OpCode::TYPE_MUL:
                case OpCode::TYPE_PLUS:
                case OpCode::TYPE_MINUS:
                case OpCode::TYPE_DIV:
                    $result = $block->getOperand($op->arg1);
                    $context->makeVariableFromRValueOp(
                        $context->helper->numericBinaryOp(
                            self::BINARYOP_MAP[$op->type], 
                            $result, 
                            $context->getVariableFromOp($block->getOperand($op->arg2)), 
                            $context->getVariableFromOp($block->getOperand($op->arg3))
                        ),
                        $result
                    );
                    break;
                case OpCode::TYPE_GREATER:
                case OpCode::TYPE_SMALLER:
                case OpCode::TYPE_IDENTICAL:
                    $result = $block->getOperand($op->arg1);
                    $context->makeVariableFromRValueOp(
                        $context->helper->compareOp(
                            self::COMPARE_MAP[$op->type], 
                            $result, 
                            $context->getVariableFromOp($block->getOperand($op->arg2)), 
                            $context->getVariableFromOp($block->getOperand($op->arg3))
                        ),
                        $result
                    );
                    break;
                case OpCode::TYPE_JUMP:
                    $newBlock = self::compileBlockInternal($context, $func, $op->block1, ...$args);
                    $context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_jump(
                        $gccBlock,
                        $context->location(),
                        $newBlock
                    );
                    return $gccBlock;
                case OpCode::TYPE_JUMPIF:
                    $if = self::compileBlockInternal($context, $func, $op->block1, ...$args);
                    $else = self::compileBlockInternal($context, $func, $op->block2, ...$args);
                    $condition = $context->castToBool(
                        $context->getVariableFromOp($block->getOperand($op->arg1))->rvalue
                    );

                    $context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_conditional(
                        $gccBlock,
                        $context->location(),
                        $condition,
                        $if,
                        $else
                    );
                    return $gccBlock;
                case OpCode::TYPE_RETURN_VOID:
                    $context->freeDeadVariables($func, $gccBlock, $block);
                    goto void_return;
                    break;
                case OpCode::TYPE_RETURN:
                    $return = $context->getVariableFromOp($block->getOperand($op->arg1));
                    $return->addref($gccBlock);
                    $context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_return(
                        $gccBlock,
                        $context->location(),
                        $return->rvalue
                    );
                    return $gccBlock;
                case OpCode::TYPE_FUNCDEF:
                    $nameOp = $block->getOperand($op->arg1);
                    assert($nameOp instanceof Operand\Literal);
                    $context->pushScope();
                    self::compileBlock($context, $op->block1, $nameOp->value);
                    $context->popScope();
                    break;
                case OpCode::TYPE_FUNCCALL_INIT:
                    $nameOp = $block->getOperand($op->arg1);
                    if (!$nameOp instanceof Operand\Literal) {
                        throw new \LogicException("Variable function calls not yet supported");
                    }
                    $lcname = strtolower($nameOp->value);
                    if (!isset($context->functions[$lcname])) {
                        throw new \RuntimeException("Call to undefined function $lcname");
                    }
                    $context->scope->toCall = $context->functions[$lcname];
                    $context->scope->args = [];
                    break;
                case OpCode::TYPE_ARG_SEND:
                    $context->scope->args[] = $context->getVariableFromOp($block->getOperand($op->arg1))->rvalue;
                    break;
                case OpCode::TYPE_FUNCCALL_EXEC_NORETURN:
                    if (is_null($context->scope->toCall)) {
                        // short circuit
                        break;
                    }
                    $context->helper->eval($gccBlock,
                        \gcc_jit_context_new_call(
                            $context->context,
                            $context->location(),
                            $context->scope->toCall,
                            count($context->scope->args),
                            \gcc_jit_rvalue_ptr_ptr::fromArray(...$context->scope->args)
                        )
                    );
                    break;
                case OpCode::TYPE_FUNCCALL_EXEC_RETURN:
                    $result = \gcc_jit_context_new_call(
                        $context->context,
                        $context->location(),
                        $context->scope->toCall,
                        count($context->scope->args),
                        \gcc_jit_rvalue_ptr_ptr::fromArray(...$context->scope->args)
                    );
                    $context->helper->assign(
                        $gccBlock,
                        $context->getVariableFromOp($block->getOperand($op->arg1))->lvalue,
                        $result
                    );
                    break;
                case OpCode::TYPE_DECLARE_CLASS:
                    $context->pushScope();
                    $context->scope->classId = $context->type->object->declareClass($block->getOperand($op->arg1));
                    self::compileClass($context, $op->block1, $context->scope->classId);
                    $context->popScope();
                    break;
                case OpCode::TYPE_NEW:
                    $class = $context->type->object->lookupOperand($block->getOperand($op->arg2));
                    $context->helper->assign(
                        $gccBlock,
                        $context->getVariableFromOp($block->getOperand($op->arg1))->lvalue,
                        $context->type->object->allocate($class)
                    );
                    $context->scope->toCall = null;
                    $context->scope->args = [];
                    break;
                case OpCode::TYPE_PROPERTY_FETCH:
                    $result = $block->getOperand($op->arg1);
                    $obj = $block->getOperand($op->arg2);
                    $name = $block->getOperand($op->arg3);
                    assert($name instanceof Operand\Literal);
                    assert($obj->type->type === Type::TYPE_OBJECT);
                    $context->scope->variables[$result] = $context->type->object->propertyFetch(
                        $context->getVariableFromOp($obj)->rvalue,
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

    private static function compileClass(Context $context, ?Block $block, int $classId) {
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
                    $context->type->object->defineProperty($classId, $name->value, $type);
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