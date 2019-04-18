<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\Type;

use PHPCompiler\JIT\Builtin\Type;

class HashTable extends Type {
    private \gcc_jit_struct_ptr $struct;
    public \gcc_jit_type_ptr $pointer;
    private \gcc_jit_struct_ptr $bucketStruct;
    public \gcc_jit_type_ptr $bucketPointer;
    private \gcc_jit_lvalue_ptr $size;

    protected array $fields;

    protected array $bucketFields;

    public function register(): void {
        $this->struct = \gcc_jit_context_new_opaque_struct(
            $this->context->context,
            null,
            '__ht__'
        );
        $this->context->registerType(
            '__ht__',
            \gcc_jit_struct_as_type($this->struct)
        );
        $this->bucketStruct = \gcc_jit_context_new_opaque_struct(
            $this->context->context,
            null,
            '__htbucket__'
        );
        $this->context->registerType(
            '__htbucket__',
            \gcc_jit_struct_as_type($this->struct)
        );
        $this->pointer = $this->context->getTypeFromString('__ht__*');
        $this->bucketPointer = $this->context->getTypeFromString('__htbucket__*');
    }

    public function implement(): void {
        $this->size = \gcc_jit_context_new_global(
            $this->context->context,
            null,
            \GCC_JIT_GLOBAL_INTERNAL,
            $this->context->getTypeFromString('size_t'),
            '__ht__size'
        );
        $this->fields = [
            'refcount' => $this->context->refcount->asField('refcount'),
            'flags' => $this->context->helper->createField('flags', 'unsigned char'),
            'size' => $this->context->helper->createField('size', 'size_t'),
            'mask' => $this->context->helper->createField('mask', 'size_t'),
            'numUsed' => $this->context->helper->createField('numUsed', 'size_t'),
            'numElements' => $this->context->helper->createField('numElements', 'size_t'),
            'nextFreeElement' => $this->context->helper->createField('nextFreeElement', 'size_t'),
            'indexes' => $this->context->helper->createField('indexes', 'size_t*'),
            'buckets' => $this->context->helper->createField('buckets', '__htbucket__*'),
        ];
        \gcc_jit_struct_set_fields(
            $this->struct,
            null,
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->fields))
        );
        $this->bucketFields = [
            'hash' => $this->context->helper->createField('hash', 'size_t'),
            'key' => $this->context->helper->createField('key', '__string__*'),
            'value' => $this->context->helper->createField('value', '__value__')
        ];
        \gcc_jit_struct_set_fields(
            $this->bucketStruct,
            null,
            count($this->bucketFields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->bucketFields))
        );
    }
}
