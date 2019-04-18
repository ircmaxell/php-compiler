<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\MemoryManager;

use PHPCompiler\JIT\Builtin\MemoryManager;

class PHP extends MemoryManager
{
    public function register(): void
    {
        parent::register();
        $this->context->helper->importFunction(
            '_efree',
            'void',
            false,
            'void*',
            ...$this->expandDebugDecl()
        );
        $this->context->helper->importfunction(
            '_emalloc',
            'void*',
            false,
            'size_t',
            ...$this->expandDebugDecl()
        );
        $this->context->helper->importFunction(
            '_erealloc',
            'void*',
            false,
            'void*',
            'size_t',
            ...$this->expandDebugDecl()
        );
    }

    /*
    public function malloc(\gcc_jit_rvalue_ptr $size, \gcc_jit_type_ptr $type): \gcc_jit_rvalue_ptr
    {
        $void = $this->context->helper->call(
            '_emalloc',
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

    public function realloc(\gcc_jit_rvalue_ptr $ptr, \gcc_jit_rvalue_ptr $size, \gcc_jit_type_ptr $type): \gcc_jit_rvalue_ptr
    {
        $void = $this->context->helper->call(
            '_erealloc',
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

    public function free(
        \gcc_jit_block_ptr $block,
        \gcc_jit_rvalue_ptr $ptr
    ): void {
        $this->context->helper->eval(
            $block,
            $this->context->helper->call(
                '_efree',
                $this->context->helper->cast($ptr, 'void*'),
                ...$this->expandDebugArgs()
            )
        );
    }

     */
    private function expandDebugDecl(): array
    {
        if (\PHP_DEBUG) {
            return [
                'const char*',
                'uint32_t',
                'const char*',
                'uint32_t',
            ];
        }

        return [];
    }

    private function expandDebugArgs(): array
    {
        if (\PHP_DEBUG) {
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
                ),
            ];
        }

        return [];
    }
}
