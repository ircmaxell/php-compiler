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

    protected function register(int $type): void {
        $this->context->register(
            'efree',
            $this->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'efree',
                'void',
                false,
                'void*',
                ...$this->expandDebugDecl()
            )
        );
        $this->context->register(
            'emalloc',
            $this->createFunction(
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


}