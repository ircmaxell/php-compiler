<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT;

use PHPCompiler\Backend\VM\Handler;


class Helper {

    private Context $context;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function eval(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $value): void {
        \gcc_jit_block_add_eval($block, null, $value);
    }

    public function call(
        string $func, 
        \gcc_jit_rvalue_ptr ...$params
    ): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_call(
            $this->context->context,
            null,
            $this->context->lookupFunction($func)->func,
            count($params),
            \gcc_jit_rvalue_ptr_ptr::fromArray(
                ...$params
            )
        );
    }

    public function cast(\gcc_jit_rvalue_ptr $from, string $to): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_cast(
            $this->context->context,
            null,
            $from,
            $this->context->getTypeFromString($to)
        );
    }

    public function binaryOp(
        int $op, 
        string $type, 
        \gcc_jit_rvalue_ptr $left,
        \gcc_jit_rvalue_ptr $right
    ): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            $op, 
            $this->context->getTypeFromString($type), 
            $left,
            $right
        );
    }

    public function importFunction(
        string $funcName, 
        string $returnType, 
        bool $isVariadic, 
        string ...$params
    ): void {
        $this->context->registerFunction($funcName, $this->createFunction(
            \GCC_JIT_FUNCTION_IMPORTED,
            $funcName,
            $returnType,
            $isVariadic,
            ...$params
        ));
    }

    public function createFunction(
        int $type, 
        string $funcName, 
        string $returnType, 
        bool $isVariadic, 
        string ...$params
    ): Func {
        $paramPointers = [];
        $i = 0;
        foreach ($params as $param) {
            $paramPointers[] = \gcc_jit_context_new_param (
                $this->context->context, 
                null, 
                $this->context->getTypeFromString($param), 
                "{$funcName}_{$i}"
            );
        }
        $gccReturnType = $this->context->getTypeFromString($returnType);
        return new Func(
            $funcName,
            \gcc_jit_context_new_function(
                $this->context->context, 
                null,
                $type,
                $gccReturnType,
                $funcName,
                count($paramPointers), 
                \gcc_jit_param_ptr_ptr::fromArray(
                    ...$paramPointers
                ),
                $isVariadic ? 1 : 0
            ),
            $gccReturnType,
            ...$paramPointers
        );
    }

    public function assign(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $result,
        \gcc_jit_rvalue_ptr $value
    ): void {
        \gcc_jit_block_add_assignment(
            $block,
            null,
            $result,
            $value
        );
    }

    public function createField(string $name, string $type): \gcc_jit_field_ptr {
        return \gcc_jit_context_new_field(
            $this->context->context,
            null,
            $this->context->getTypeFromString($type),
            $name
        );
    }

}