<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT\Builtin;

use PHPCompiler\Backend\VM\JIT\Builtin;

class Refcount extends Builtin {
    private \gcc_jit_struct_ptr $struct;
    public \gcc_jit_type_ptr $type;
    private array $fields;

    protected function register(): void {
        $this->fields = [
            $this->context->helper->createField('refcount', 'int'),
            $this->context->helper->createField('typeinfo', 'int')
        ];
        $this->struct = \gcc_jit_context_new_struct_type(
            $this->context->context,
            null,
            '__ref__',
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...$this->fields)
        );
        $this->type = \gcc_jit_struct_as_type($this->struct);
        $this->context->registerType(
            '__ref__',
            $this->type
        );
    }

    public function asField(): \gcc_jit_field_ptr {
        return \gcc_jit_context_new_field(
            $this->context->context,
            null,
            $this->type,
            '__ref__'
        );
    }
}