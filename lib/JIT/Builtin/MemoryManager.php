<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;
use PHPCompiler\JIT\Context;

abstract class MemoryManager extends Builtin {

    abstract public function malloc(
        \gcc_jit_rvalue_ptr $size, 
        \gcc_jit_type_ptr $type
    ): \gcc_jit_rvalue_ptr;

    abstract public function realloc(
        \gcc_jit_rvalue_ptr $ptr, 
        \gcc_jit_rvalue_ptr $size, 
        \gcc_jit_type_ptr $type
    ): \gcc_jit_rvalue_ptr;

    abstract public function free(
        \gcc_jit_block_ptr $block,
        \gcc_jit_rvalue_ptr $ptr
    ): void;

    public static function load(Context $context, int $loadType): self {
        if ($loadType === Builtin::LOAD_TYPE_STANDALONE) {
            return new MemoryManager\Native($context, $loadType);
        }
        return new MemoryManager\PHP($context, $loadType);
    }

    public function register(): void {
        $this->context->registerFunction(
            'memcpy',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'memcpy',
                'int',
                false,
                'char*',
                'const char*',
                'size_t'
            )
        );
        $this->context->registerFunction(
            'memset',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'memset',
                'int',
                false,
                'char*',
                'char',
                'size_t'
            )
        );
    }   

    public function memcpy(
        \gcc_jit_block_ptr $block,
        \gcc_jit_rvalue_ptr $to,
        \gcc_jit_rvalue_ptr $from,
        \gcc_jit_rvalue_ptr $size
    ): void {
        $this->context->helper->eval($block, $this->context->helper->call(
            'memcpy',
            $to,
            $this->context->helper->cast($from, 'const char*'),
            $size
        ));
    }

    public function memset(
        \gcc_jit_block_ptr $block,
        \gcc_jit_rvalue_ptr $dest,
        \gcc_jit_rvalue_ptr $value,
        \gcc_jit_rvalue_ptr $size
    ): void {
        $this->context->helper->eval($block, $this->context->helper->call(
            'memset',
            $dest,
            $value,
            $size
        ));
    }

}