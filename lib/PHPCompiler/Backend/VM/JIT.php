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
        $context->setDebug(true);
        if (!is_null($debugfile)) {
            $context->setDebugFile($debugfile);
        }
        // $context->setOption(
        //     \GCC_JIT_INT_OPTION_OPTIMIZATION_LEVEL,
        //     self::$optimizationLevel
        // );

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

    private static function handlePhiNodes(
        JIT\Context $context, 
        \gcc_jit_function_ptr $func, 
        \gcc_jit_block_ptr $gccBlock,
        Block $block
    ): void {
        $seen = new \SplObjectStorage;
        $queue = [$block->orig];
        while (!empty($queue)) {
            $cfgBlock = array_pop($queue);
            if ($seen->contains($cfgBlock)) {
                continue;
            }

            $seen->attach($cfgBlock);
            foreach ($cfgBlock->phi as $phi) {
                self::registerPhi($context, $func, $gccBlock, $phi);
            }
            $jump = end($cfgBlock->children);
            if ($jump instanceof Op\Stmt\Jump) {
                $queue[] = $jump->target;
            } elseif ($jump instanceof Op\Stmt\JumpIf) {
                $queue[] = $jump->if;
                $queue[] = $jump->else;
            }
        }
    }

    private static function registerPhi(
        JIT\Context $context,
        \gcc_jit_function_ptr $func, 
        \gcc_jit_block_ptr $block,
        Op\Phi $phi
    ): void {
        foreach ($phi->vars as $var) {
            if (self::$paramStorage->contains($var)) {
                self::registerPhiLVal(
                    $phi, 
                    \gcc_jit_param_as_lvalue(self::$paramStorage[$var])
                );
                return;
            }
        }
        $lval = self::makeLValue($context, $func, $block, $phi->result);
        self::registerPhiLVal($phi, $lval);
    }

    private static function registerPhiLVal(Op\Phi $phi, \gcc_jit_lvalue_ptr $lval): void {
        self::$lvalueStorage[$phi->result] = $lval;
        foreach ($phi->vars as $var) {
            self::$lvalueStorage[$var] = $lval;  
        }
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
        if (self::$blockNumber === 1) {
            //Todo: this is wrong, it should be run for the first block in each function, not the first overall
            self::handlePhiNodes($context, $func, $gccBlock, $block);
        }

        foreach ($block->opCodes as $op) {
            switch ($op->type) {
                case OpCode::TYPE_ASSIGN:
                    $result = self::getRValue($context, $block, $op->arg3);
                    \gcc_jit_block_add_assignment(
                        $gccBlock,
                        null,
                        self::getLValue($context, $func, $gccBlock, $block, $op->arg1),
                        $result
                    );
                    \gcc_jit_block_add_assignment(
                        $gccBlock,
                        null,
                        self::getLValue($context, $func, $gccBlock, $block, $op->arg2),
                        $result
                    );
                    break;  
                case OpCode::TYPE_JUMP:
                    $newBlock = self::compileBlockInternal(
                        $context,
                        $func,
                        $op->block1
                    );
                    \gcc_jit_block_end_with_jump(
                        $gccBlock,
                        null,
                        $newBlock
                    );
                    return $gccBlock;
                case OpCode::TYPE_JUMPIF:
                    $if = self::compileBlockInternal(
                        $context,
                        $func,
                        $op->block1
                    );
                    $else = self::compileBlockInternal(
                        $context,
                        $func,
                        $op->block2
                    );
                    \gcc_jit_block_end_with_conditional(
                        $gccBlock,
                        null,
                        self::getRValue($context, $block, $op->arg1),
                        $if,
                        $else
                    );
                    return $gccBlock;
                case OpCode::TYPE_ECHO:
                    $arg = self::getRValue($context, $block, $op->arg1);
                    $length = $context->type->string->toSizeRValue($arg);
                    \gcc_jit_block_add_eval(
                        $gccBlock,
                        null,
                        \gcc_jit_context_new_call(
                            $context->context,
                            null,
                            $context->lookupFunction('printf')->func,
                            3,
                            \gcc_jit_rvalue_ptr_ptr::fromArray(
                                $context->constantFromString('%.*s'),
                                $length,
                                $context->type->string->toValueRValue($arg)
                            )
                        )
                    );
                    break;
                case OpCode::TYPE_PLUS:
                    $result = $block->getOperand($op->arg1);
                    self::setRValue(\gcc_jit_context_new_binary_op(
                        $context->context,
                        null,
                        \GCC_JIT_BINARY_OP_PLUS,
                        $context->getTypeFromType($result->type),
                        self::getRValue($context, $block, $op->arg2),
                        self::getRValue($context, $block, $op->arg3),
                    ), $result);
                    break;
                case OpCode::TYPE_SMALLER:
                    $result = $block->getOperand($op->arg1);
                    self::setRValue(\gcc_jit_context_new_comparison(
                        $context->context,
                        null,
                        \GCC_JIT_COMPARISON_LT,
                        self::getRValue($context, $block, $op->arg2),
                        self::getRValue($context, $block, $op->arg3),
                    ), $result);
                    break;
                case OpCode::TYPE_RETURN_VOID:
                    goto void_return;
                case OpCode::TYPE_CONCAT:
                    $result = self::getLValue($context, $func, $gccBlock, $block, $op->arg1);
                    $left = self::getRValue($context, $block, $op->arg2);
                    $right =
                        self::getRValue($context, $block, $op->arg3);
                    $context->refcount->delref($gccBlock, $result->asRValue());
                    $leftLength = $context->type->string->toSizeRValue($left);
                    $rightLength = $context->type->string->toSizeRValue($right);
                    $context->type->string->allocate(
                        $gccBlock, 
                        $result, 
                        $context->helper->binaryOp(
                            \GCC_JIT_BINARY_OP_PLUS,
                            'size_t',
                            $leftLength,
                            $rightLength
                        )
                    );
                    $context->memory->memcpy(
                        $gccBlock,
                        $context->type->string->valuePtr($result->asRValue()),
                        $context->type->string->toValueRValue($left),
                        $leftLength
                    );
                    $context->memory->memcpy(
                        $gccBlock,
                        gcc_jit_lvalue_get_address(gcc_jit_context_new_array_access(
                            $context->context,
                            $context->location(),
                            $context->type->string->valuePtr($result->asRValue()),
                            $context->helper->cast($leftLength, 'size_t')
                        ), $context->location()),
                        $context->type->string->toValueRValue($right),
                        $rightLength
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

    public static function getLValue(
        JIT\Context $context, 
        \gcc_jit_function_ptr $func,
        \gcc_jit_block_ptr $gccBlock, 
        Block $block, 
        int $scopePointer
    ): \gcc_jit_lvalue_ptr {
        $op = $block->getOperand($scopePointer);
        if (isset(self::$lvalueStorage[$op])) {
            return self::$lvalueStorage[$op];
        } elseif (isset(self::$paramStorage[$op])) {
            return \gcc_jit_param_as_lvalue(self::$paramStorage[$op]);
        } elseif (isset(self::$rvalueStorage[$op])) {
            throw new \LogicException("Cannot cast rvalue to literal for operand");
        }
        if ($op instanceof Operand\Temporary) {
            if (is_null($op->type)) {
                var_dump($block);
            }
            self::$lvalueStorage[$op] = self::makeLValue($context, $func, $gccBlock, $op);
            return self::$lvalueStorage[$op];
        }
        throw new \LogicException("Could not extract lvalue for " . get_class($op));
    }

    public static function setRValue(\gcc_jit_rvalue_ptr $rvalue, Operand $op): void {
        assert(!self::$rvalueStorage->contains($op));
        assert(!self::$lvalueStorage->contains($op));
        self::$rvalueStorage[$op] = $rvalue;
    }

    public static int $lvalueCounter = 0;

    public static function makeLValue(
        JIT\Context $context, 
        \gcc_jit_function_ptr $func,
        \gcc_jit_block_ptr $block, 
        Operand $op
    ): \gcc_jit_lvalue_ptr {
        assert(!self::$lvalueStorage->contains($op));
        assert(!self::$rvalueStorage->contains($op));
        assert(!is_null($op->type));
        if (is_null($op->type)) {
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        self::$lvalueCounter++;
        $lval = \gcc_jit_function_new_local(
            $func,
            null,
            $context->getTypeFromType($op->type),
            "lvalue_" . self::$lvalueCounter
        );
        self::$lvalueStorage[$op] = $lval;
        self::$rvalueStorage[$op] = \gcc_jit_lvalue_as_rvalue($lval);
        if ($op->type->type === Type::TYPE_STRING) {
            // initialize!!!
            $context->type->string->allocate($block, $lval, $context->constantFromInteger(0, 'size_t'));
        }

        return $lval;
    }

    public static function getRValue(
        JIT\Context $context,
        Block $block, 
        int $scopePointer
    ): \gcc_jit_rvalue_ptr {
        $op = $block->getOperand($scopePointer);
        if (isset(self::$rvalueStorage[$op])) {
            return self::$rvalueStorage[$op];
        } elseif (isset(self::$lvalueStorage[$op])) {
            return \gcc_jit_lvalue_as_rvalue(self::$lvalueStorage[$op]);
        } elseif (isset(self::$paramStorage[$op])) {
            return \gcc_jit_param_as_rvalue(self::$paramStorage[$op]);
        }
        if ($op instanceof Operand\Literal) {
            // Compile Constants
            switch ($op->type->type) {
                case Type::TYPE_STRING:
                    $const = $context->constantStringFromString($op->value);
                    self::$rvalueStorage[$op] = $const;
                    return $const;
                case Type::TYPE_LONG:
                    $const = $context->constantFromInteger($op->value);
                    self::$rvalueStorage[$op] = $const;
                    return $const;
            }
        }
    }

    public static function compileArg(Operand $op): \gcc_jit_param_ptr {
        throw new \LogicException("Block args not implemented yet");
    }



}