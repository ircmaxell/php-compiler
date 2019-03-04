<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\MemoryManager;

use PHPCompiler\JIT\Builtin\MemoryManager;

class Native extends MemoryManager {

    public function register(): void {
        parent::register();
        $this->context->registerFunction(
            'free',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'free',
                'void',
                false,
                'void*'
            )
        );
        $this->context->registerFunction(
            'malloc',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'malloc',
                'void*',
                false,
                'size_t'
            )
        );
        $this->context->registerFunction(
            'realloc',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'realloc',
                'void*',
                false,
                'void*',
                'size_t'
            )
        );
    } 

    public function malloc(\gcc_jit_rvalue_ptr $size, \gcc_jit_type_ptr $type): \gcc_jit_rvalue_ptr {
        $void = $this->context->helper->call(
            'malloc', 
            $size 
        );
        return \gcc_jit_context_new_cast(
            $this->context->context,
            null,
            $void,
            $type
        );
    }

    public function realloc(
        \gcc_jit_rvalue_ptr $ptr, 
        \gcc_jit_rvalue_ptr $size, 
        \gcc_jit_type_ptr $type
    ): \gcc_jit_rvalue_ptr {
        $void = $this->context->helper->call(
            'realloc', 
            \gcc_jit_context_new_cast(
                $this->context->context,
                $this->context->location(),
                $ptr,
                $this->context->getTypeFromString('void*')
            ),
            $size
        );
        return \gcc_jit_context_new_cast(
            $this->context->context,
            null,
            $void,
            $type
        );
    }

    public function free(
        \gcc_jit_block_ptr $block,
        \gcc_jit_rvalue_ptr $ptr
    ): void {
        $this->context->helper->eval(
            $block,
            $this->context->helper->call(
                'free', 
                $this->context->helper->cast($ptr, 'void*')
            )
        );
    }

}
