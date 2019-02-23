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
    private array $fields;

    protected function register(): void {
        $this->struct = \gcc_jit_context_new_opaque_struct(
            $this->context->context,
            null,
            '__string__'
        );
        $this->context->registerType(
            '__string__',
            \gcc_jit_struct_as_type($this->struct)
        );
        $this->pointer = \gcc_jit_type_get_pointer($this->context->getTypeFromString('__string__'));
        $this->context->registerType(
            '__string__*',
            $this->pointer
        );
    }

    public function implement(): void {
        $this->fields = [
            'refcount' => $this->context->refcount->asField('refcount'),
            'size' => $this->context->helper->createField('size', 'size_t'),
            'value' => $this->context->helper->createField('value', 'char[1]'),
        ];
        \gcc_jit_struct_set_fields(
            $this->struct,
            null,
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->fields))
        );
    }

    public function readSize(\gcc_jit_rvalue_ptr $struct): \gcc_jit_rvalue_ptr {
        return gcc_jit_rvalue_access_field(
            $struct,
            null,
            $this->fields['size']
        );
    }

    public function readValue(\gcc_jit_rvalue_ptr $struct): \gcc_jit_rvalue_ptr {
        return gcc_jit_rvalue_access_field(
            $struct,
            null,
            $this->fields['value']
        );
    }

    public function writeSize(\gcc_jit_lvalue_ptr $struct): \gcc_jit_lvalue_ptr {
        return gcc_jit_lvalue_access_field(
            $struct,
            null,
            $this->fields['size']
        );
    }


    public function writeValue(\gcc_jit_lvalue_ptr $struct): \gcc_jit_lvalue_ptr {
        return gcc_jit_lvalue_access_field(
            $struct,
            null,
            $this->fields['value']
        );
    }

    public function sizePtr(\gcc_jit_rvalue_ptr $ptr): \gcc_jit_lvalue_ptr {
        return \gcc_jit_rvalue_dereference_field($ptr, null, $this->fields['size']);
    }
    public function valuePtr(\gcc_jit_rvalue_ptr $ptr): \gcc_jit_lvalue_ptr {
        return \gcc_jit_rvalue_dereference_field($ptr, null, $this->fields['value']);
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
        $this->context->helper->eval($block, $this->context->helper->call(
            'printf', 
            $this->context->constantFromString("String Size: %d\n"),
            $this->size
        ));
        $const = $this->constant($block, 'foo');
        $this->context->helper->eval($block, $this->context->helper->call(
            'printf', 
            $this->context->constantFromString("Length of 'foo': %d\n"),
            \gcc_jit_lvalue_as_rvalue($this->sizePtr($const))
        ));
        return $block;
    }

    public function constant(\gcc_jit_block_ptr $block, string $value): \gcc_jit_rvalue_ptr {
        $size = $this->context->helper->binaryOp(
            GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $this->size,
            $this->context->helper->cast($this->context->constantFromInteger(strlen($value) + 1), 'size_t')
        );
        $ptr = $this->context->memory->emalloc($size, $this->pointer);
        $this->context->helper->assign(
            $block,
            $this->writeSize(\gcc_jit_rvalue_dereference($ptr, null)),
            $this->context->helper->cast($this->context->constantFromInteger(strlen($value)), 'size_t')
        );
        $this->context->helper->assign(
            $block, 
            $this->writeValue(\gcc_jit_rvalue_dereference($ptr, null)),
            $this->context->constantFromString($value)
        );
        return $ptr;
    }

}