<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT\Builtin;

use PHPCompiler\Backend\VM\JIT\Builtin;
use PHPCompiler\Backend\VM\JIT\Context;
use PHPCompiler\Backend\VM\JIT\Func;
use PHPCompiler\Backend\VM\JIT;

class MemoryManager extends Builtin {

    protected function register(): void {
        $this->context->registerFunction(
            'efree',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'efree',
                'void',
                false,
                'void*',
                ...$this->expandDebugDecl()
            )
        );
        $this->context->registerFunction(
            'emalloc',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                '_emalloc',
                'void',
                false,
                'void*',
                ...$this->expandDebugDecl()
            )
        );
    }

    private function expandDebugDecl(): array {
        if (PHP_DEBUG) {
            return [
                'const char*',
                'uint32_t',
                'const char*',
                'uint32_t',
            ];
        }
        return [];
    }

    private function expandDebugArgs(): array {
        if (PHP_DEBUG) {
            return [
                // TODO: convert to null pointers
                null, 
                null, 
                null, 
                null
            ];
        }
        return [];
    }

    public function emalloc(\gcc_jit_rvalue_ptr $size, \gcc_jit_type_ptr $type): \gcc_jit_rvalue_ptr {
        $void = $this->context->helper->call('emalloc', $size, ...$this->expandDebugArgs());
        return \gcc_jit_context_new_cast(
            $this->context->context,
            null,
            $void,
            $type
        );
    }

}