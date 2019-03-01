<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

use PHPCfg\Operand;
use PHPCfg\Op;
use PHPCfg\Block as CfgBlock;
use PHPTypes\Type;


class JIT {
    private static int $functionNumber = 0;

    public static int $optimizationLevel = 3;

    private static array $valueMap = [];
    private static \SplObjectStorage $rvalueStorage;
    private static \SplObjectStorage $lvalueStorage;
    private static \SplObjectStorage $paramStorage;
    private static \SplObjectStorage $blockStorage;
    private static array $stringConstant = [];
    private static array $intConstant = [];
    private static array $builtIns = [];
    
    private static function init(): void {
        self::$functionNumber++;
        self::$valueMap = [];
        self::$rvalueStorage = new \SplObjectStorage;
        self::$lvalueStorage = new \SplObjectStorage;
        self::$paramStorage = new \SplObjectStorage;
        self::$blockStorage = new \SplObjectStorage;
    }

    public static function compileBlock(Block $block, ?string $debugfile = null) {
        self::init();
        $funcName = "internal_" . self::$functionNumber;
        $context = new JIT\Context(JIT\Builtin::LOAD_TYPE_EMBED);
        //gcc_jit_context_set_bool_option($context->context, GCC_JIT_BOOL_OPTION_DUMP_INITIAL_GIMPLE, 1);
        // $context->setDebug(true);
        // if (!is_null($debugfile)) {
        //     $context->setDebugFile($debugfile);
        // }
        $context->setOption(
            \GCC_JIT_INT_OPTION_OPTIMIZATION_LEVEL,
            self::$optimizationLevel
        );

        $callbackType = 'void(*)(';
        $callbackSep = '';
        $args = [];
        foreach ($block->args as $arg) {
            $args[] = self::compileArg($arg);
        }
        $callbackType .= ')';
        $func = \gcc_jit_context_new_function(
            $context->context, 
            NULL,
            \GCC_JIT_FUNCTION_EXPORTED,
            $context->getTypeFromString('void'),
            $funcName,
            count($args), 
            \gcc_jit_param_ptr_ptr::fromArray(...$args),
            0
        );
        self::compileBlockInternal($context, $func, $block);
        $block->handler = $context->compileInPlace()->getHandler($funcName, $callbackType);
        self::init();
    }

    private static int $blockNumber = 0;
    public function compileBlockInternal(
        JIT\Context $context, 
        \gcc_jit_function_ptr $func,
        Block $block
    ): \gcc_jit_block_ptr {
        if (self::$blockStorage->contains($block)) {
            return self::$blockStorage[$block];
        }
        self::$blockNumber++;
        $gccBlock = \gcc_jit_function_new_block($func, 'block_' . self::$blockNumber);
        self::$blockStorage[$block] = $gccBlock;
        // Handle hoisted variables
        foreach ($block->orig->hoistedOperands as $operand) {
            $var = $context->makeVariableFromOp($func, $gccBlock, $block, $operand);
        }

        foreach ($block->opCodes as $op) {
            switch ($op->type) {
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
                case OpCode::TYPE_PLUS:
                    $result = $block->getOperand($op->arg1);
                    $context->makeVariableFromRValueOp(
                        $context->helper->numericBinaryOp(
                            \GCC_JIT_BINARY_OP_PLUS, 
                            $result, 
                            $context->getVariableFromOp($block->getOperand($op->arg2)), 
                            $context->getVariableFromOp($block->getOperand($op->arg3))
                        ),
                        $result
                    );
                    break;
                case OpCode::TYPE_SMALLER:
                    $result = $block->getOperand($op->arg1);
                    $context->makeVariableFromRValueOp(
                        $context->helper->compareOp(
                            \GCC_JIT_COMPARISON_LT, 
                            $result, 
                            $context->getVariableFromOp($block->getOperand($op->arg2)), 
                            $context->getVariableFromOp($block->getOperand($op->arg3))
                        ),
                        $result
                    );
                    break;
                case OpCode::TYPE_JUMP:
                    $newBlock = self::compileBlockInternal($context, $func, $op->block1);
                    $context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_jump(
                        $gccBlock,
                        $context->location(),
                        $newBlock
                    );
                    return $gccBlock;
                case OpCode::TYPE_JUMPIF:
                    $if = self::compileBlockInternal($context, $func, $op->block1);
                    $else = self::compileBlockInternal($context, $func, $op->block2);
                    $context->freeDeadVariables($func, $gccBlock, $block);
                    \gcc_jit_block_end_with_conditional(
                        $gccBlock,
                        $context->location(),
                        $context->getVariableFromOp($block->getOperand($op->arg1))->rvalue,
                        $if,
                        $else
                    );
                    return $gccBlock;
                case OpCode::TYPE_RETURN_VOID:
                    $context->freeDeadVariables($func, $gccBlock, $block);
                    goto void_return;
                    break;
                default:
                    throw new \LogicException("Unknown JIT opcode: ". $op->getType());
            }
        }
void_return:
        \gcc_jit_block_end_with_void_return($gccBlock, null);
        return $gccBlock;
    }

    public static function compileArg(Operand $op): \gcc_jit_param_ptr {
        throw new \LogicException("Block args not implemented yet");
    }



}