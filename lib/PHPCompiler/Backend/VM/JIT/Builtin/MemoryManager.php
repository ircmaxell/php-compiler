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
        $this->context->registerFunction(
            'efree',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                '_efree',
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
                'void*',
                false,
                'size_t',
                ...$this->expandDebugDecl()
            )
        );
        $this->context->registerFunction(
            'erealloc',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                '_erealloc',
                'void*',
                false,
                'void*',
                'size_t',
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
                $this->context->constantFromString('jit'), 
                $this->context->helper->cast(
                    $this->context->constantFromInteger(2),
                    'uint32_t'
                ), 
                $this->context->constantFromString('jit'), 
                $this->context->helper->cast(
                    $this->context->constantFromInteger(2),
                    'uint32_t'
                ) 
            ];
        }
        return [];
    }

    public function efree(\gcc_jit_rvalue_ptr $ptr): \gcc_jit_rvalue_ptr {
        return $this->context->helper->call(
            'efree', 
            $this->context->helper->cast($ptr, 'void*'),
            ...$this->expandDebugArgs()
        );
    }

    public function erealloc(\gcc_jit_rvalue_ptr $ptr, \gcc_jit_rvalue_ptr $size, \gcc_jit_type_ptr $type): \gcc_jit_rvalue_ptr {
        $void = $this->context->helper->call(
            'erealloc', 
            \gcc_jit_context_new_cast(
                $this->context->context,
                $this->context->location(),
                $ptr,
                $this->context->getTypeFromString('void*')
            ),
            $size, 
            ...$this->expandDebugArgs()
        );
        return \gcc_jit_context_new_cast(
            $this->context->context,
            null,
            $void,
            $type
        );
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