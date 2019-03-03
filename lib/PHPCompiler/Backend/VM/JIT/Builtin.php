<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT;

use PHPCompiler\Backend\VM\JIT;

abstract class Builtin {
    const LOAD_TYPE_EXPORT = 1;
    const LOAD_TYPE_IMPORT = 2;
    const LOAD_TYPE_EMBED = 3;
    const LOAD_TYPE_STANDALONE = 4;

    protected Context $context;
    protected int $loadType;

    public function __construct(Context $context, int $loadType) {
        $this->context = $context;
        $context->registerBuiltin($this);
        $this->loadType = $loadType;
    }

    public function register(): void {
    }

    public function implement(): void {
    }

    public function initialize(): void {
    }

    protected function sizeof(\gcc_jit_type_ptr $type): \gcc_jit_rvalue_ptr {
        $type_ptr = \gcc_jit_type_get_pointer($type);
        $size_type = $this->context->getTypeFromString('size_t');
        $byte_type_ptr = \gcc_jit_type_get_pointer(
            \gcc_jit_context_get_int_type($this->context->context, 1, 0)
        );

        $one = gcc_jit_context_new_rvalue_from_int(
            $this->context->context, 
            $size_type, 
            1
        );

        $ptr_0 = gcc_jit_context_new_rvalue_from_ptr(
            $this->context->context, 
            $type_ptr, 
            null
        );
        $ptr_1 = gcc_jit_lvalue_get_address(
            gcc_jit_context_new_array_access(
                $this->context->context, 
                NULL, 
                $ptr_0, 
                $one
            ), 
            NULL
        );
        $ptr_0 = gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            \GCC_JIT_BINARY_OP_BITWISE_AND, 
            $size_type, 
            $ptr_0, 
            $ptr_0
        );
        $ptr_1 = gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            GCC_JIT_BINARY_OP_BITWISE_AND, 
            $size_type, 
            $ptr_1, 
            $ptr_1
        );

        return gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            \GCC_JIT_BINARY_OP_MINUS, 
            $size_type, 
            $ptr_1, 
            $ptr_0
        );

    }

}