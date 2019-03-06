<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\Type;

use PHPCompiler\JIT\Builtin\Type;
use PHPCompiler\JIT\Builtin\Refcount;
use PHPCompiler\JIT\Variable;

class Value extends Type {
    private \gcc_jit_struct_ptr $struct;
    public \gcc_jit_type_ptr $pointer;
    private \gcc_jit_lvalue_ptr $size;
    private \gcc_jit_type_ptr $unionType;

    protected array $fields;
    protected array $union;

    public function register(): void {
        $this->struct = \gcc_jit_context_new_opaque_struct(
            $this->context->context,
            null,
            '__value__'
        );
        $this->context->registerType(
            '__value__',
            \gcc_jit_struct_as_type($this->struct)
        );
        $this->pointer = \gcc_jit_type_get_pointer($this->context->getTypeFromString('__value__'));
        $this->context->registerType(
            '__value__*',
            $this->pointer
        );
    }

    public function implement(): void {
        $this->size = \gcc_jit_context_new_global(
            $this->context->context,
            null,
            \GCC_JIT_GLOBAL_INTERNAL,
            $this->context->getTypeFromString('size_t'),
            '__value__size'
        );
        $this->union = [
            'long' => $this->context->helper->createField('long', 'long long'),
            'float' => $this->context->helper->createField('float', 'double'),
            'bool' => $this->context->helper->createField('float', 'bool'),
            'string' => $this->context->helper->createField('string', '__string__*'),
            'object' => $this->context->helper->createField('object', '__object__*'),
        ];
        $this->unionType = \gcc_jit_context_new_union_type(
            $this->context->context,
            $this->context->location(),
            '__value__union__',
            count($this->union),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->union))
        );
        $this->fields = [
            'type' => $this->context->helper->createField('type', 'unsigned char'),
            'value' => \gcc_jit_context_new_field($this->context->context, $this->context->location(), $this->unionType, 'value')
        ];
        \gcc_jit_struct_set_fields(
            $this->struct,
            null,
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->fields))
        );
    }

    public function initialize(): void {
        \gcc_jit_block_add_assignment(
            $this->context->initBlock,
            null,
            $this->size,
            $this->sizeof($this->context->getTypeFromString('__value__'))
        );
    }

}