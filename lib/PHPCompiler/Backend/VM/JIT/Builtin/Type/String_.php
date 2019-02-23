<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT\Builtin\Type;

use PHPCompiler\Backend\VM\JIT\Builtin\Type;
use PHPCompiler\Backend\VM\JIT\Builtin;
use PHPCompiler\Backend\VM\JIT\Context;
use PHPCompiler\Backend\VM\JIT\Func;
use PHPCompiler\Backend\VM\JIT;

class String_ extends Type {
    private \gcc_jit_struct_ptr $struct;
    private \gcc_jit_type_ptr $pointer;
    private \gcc_jit_rvalue_ptr $size;

    protected function register(): void {
        $fields = [
            \gcc_jit_context_new_field(
                $this->context->context,
                null,
                $this->context->getTypeFromString('int'),
                'refcount'
            ),
            \gcc_jit_context_new_field(
                $this->context->context,
                null,
                $this->context->getTypeFromString('char'),
                'type_info'
            ),
            \gcc_jit_context_new_field(
                $this->context->context,
                null,
                $this->context->getTypeFromString('size_t'),
                'size'
            ),
            \gcc_jit_context_new_field(
                $this->context->context,
                null,
                $this->context->getTypeFromString('char'),
                'value'
            ),
        ];
        $this->struct = \gcc_jit_context_new_struct_type(
            $this->context->context,
            null,
            '__string__',
            count($fields),
            \gcc_jit_field_ptr_ptr::fromArray(...$fields)
        );
        $this->context->registerType(
            '__string__',
            \gcc_jit_struct_as_type($this->struct)
        );
        $this->context->registerType(
            '__string__*',
            \gcc_jit_type_get_pointer($this->context->getTypeFromString('__string__'))
        );
        $this->pointer = $this->context->getTypeFromString('__string__*');
    }

    public function init(\gcc_jit_function_ptr $func, \gcc_jit_block_ptr $block): \gcc_jit_block_ptr {
        $lval = \gcc_jit_context_new_global(
            $this->context->context,
            null,
            \GCC_JIT_GLOBAL_INTERNAL,
            $this->context->getTypeFromString('size_t'),
            '__string__size'
        );
        $this->size = \gcc_jit_lvalue_as_rvalue($lval);
        \gcc_jit_block_add_assignment(
            $block,
            null,
            $lval,
            $this->sizeof($this->context->getTypeFromString('__string__'))
        );
        \gcc_jit_block_add_eval(
            $block,
            null,
            \gcc_jit_context_new_call(
                $this->context->context,
                null,
                $this->context->lookupFunction('printf')->func,
                2,
                \gcc_jit_rvalue_ptr_ptr::fromArray(
                    $this->context->constantFromString("String Size: %d\n"),
                    $this->size
                )
            )
        );
        return $block;
    }

    public function constant(string $value): \gcc_jit_rvalue_ptr {
        $size = \gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            GCC_JIT_BINARY_OP_PLUS, 
            $size_type, 
            $this->size, 
            $this->context->constantFromInteger(strlen($value))
        );
        $ptr = $this->context->memory->emalloc($size, $this->pointer);
// todo assign
    }

}