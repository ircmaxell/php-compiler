<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

class Type extends Builtin {

    public Type\String_ $string;
    protected array $fields;

    public function register(): void {
        $this->string = new Type\String_($this->context, $this->loadType);
        $this->object = new Type\Object_($this->context, $this->loadType);
        $this->value = new Type\Value($this->context, $this->loadType);
        $this->hashtable = new Type\HashTable($this->context, $this->loadType);
        $this->array = new Type\Array_($this->context, $this->loadType);
        $this->string->register();
        $this->object->register();
        $this->value->register();
        $this->hashtable->register();
        $this->array->register();
    }

    protected function readField(string $name, \gcc_jit_rvalue_ptr $struct): \gcc_jit_rvalue_ptr {
        if (!isset($this->fields[$name])) {
            throw new \LogicException("Could not read field $name");
        }
        return gcc_jit_rvalue_dereference_field(
            $struct,
            $this->context->location(),
            $this->fields[$name]
        )->asRValue();
    }

    protected function writeField(string $name, \gcc_jit_rvalue_ptr $pointer): \gcc_jit_lvalue_ptr {
        if (!isset($this->fields[$name])) {
            throw new \LogicException("Could not write field $name");
        }
        return gcc_jit_rvalue_dereference_field(
            $pointer,
            $this->context->location(),
            $this->fields[$name]
        );
    }

    protected function sizeof(\gcc_jit_type_ptr $type): \gcc_jit_rvalue_ptr {
        $type_ptr = \gcc_jit_type_get_pointer($type);
        $size_type = $this->context->getTypeFromString('size_t');
        $byte_type_ptr = \gcc_jit_type_get_pointer(
            \gcc_jit_context_get_int_type($this->context->context, 1, 0)
        );

        $one = gcc_jit_context_new_rvalue_from_int(
            $this->context->context, 
            $size_type, 
            1
        );

        $ptr_0 = gcc_jit_context_new_rvalue_from_ptr(
            $this->context->context, 
            $type_ptr, 
            null
        );
        $ptr_1 = gcc_jit_lvalue_get_address(
            gcc_jit_context_new_array_access(
                $this->context->context, 
                NULL, 
                $ptr_0, 
                $one
            ), 
            NULL
        );
        $ptr_0 = gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            \GCC_JIT_BINARY_OP_BITWISE_AND, 
            $size_type, 
            $ptr_0, 
            $ptr_0
        );
        $ptr_1 = gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            GCC_JIT_BINARY_OP_BITWISE_AND, 
            $size_type, 
            $ptr_1, 
            $ptr_1
        );

        return gcc_jit_context_new_binary_op(
            $this->context->context, 
            NULL, 
            \GCC_JIT_BINARY_OP_MINUS, 
            $size_type, 
            $ptr_1, 
            $ptr_0
        );

    }

}