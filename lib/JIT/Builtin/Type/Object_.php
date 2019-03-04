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

class Object_ extends Type {
    private \gcc_jit_struct_ptr $struct;
    public \gcc_jit_type_ptr $pointer;
    private \gcc_jit_lvalue_ptr $size;
    protected array $fields;

    public function register(): void {
        $this->struct = \gcc_jit_context_new_opaque_struct(
            $this->context->context,
            null,
            '__object__'
        );
        $this->context->registerType(
            '__object__',
            \gcc_jit_struct_as_type($this->struct)
        );
        $this->pointer = \gcc_jit_type_get_pointer($this->context->getTypeFromString('__object__'));
        $this->context->registerType(
            '__object__*',
            $this->pointer
        );
        $this->context->registerFunction(
            '__object__alloc',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__object__alloc',
                '__object__*',
                false,
                'long long',
                'size_t'
            )
        );
    }

    public function implement(): void {
        $this->size = \gcc_jit_context_new_global(
            $this->context->context,
            null,
            \GCC_JIT_GLOBAL_INTERNAL,
            $this->context->getTypeFromString('size_t'),
            '__object_size'
        );
        $this->fields = [
            'refcount' => $this->context->refcount->asField('refcount'),
            'class_id' => $this->context->helper->createField('class_id', 'long long'),
        ];
        \gcc_jit_struct_set_fields(
            $this->struct,
            null,
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->fields))
        );
        $this->implementAlloc();
    }

    private function implementAlloc(): void {
        $alloc = $this->context->lookupFunction('__object__alloc');
        $block = \gcc_jit_function_new_block($alloc->func, 'main');
        $local = \gcc_jit_function_new_local($alloc->func, null, $this->pointer, 'result');
        $allocSize = $this->context->helper->binaryOp(
            GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $this->size->asRValue(),
            $alloc->params[1]->asRValue()
        );
        $this->context->helper->assign(
            $block, 
            $local,
            $this->context->memory->malloc($allocSize, $this->pointer) 
        );
        $this->context->helper->assign(
            $block,
            $this->writeField('class_id', $local->asRValue()),
            $alloc->params[0]->asRValue()
        );
        $this->context->refcount->init(
            $block, 
            $local->asRValue(),
            Refcount::TYPE_INFO_REFCOUNTED | Refcount::TYPE_INFO_TYPE_OBJECT
        );
        \gcc_jit_block_end_with_return($block,  null, $local->asRValue());
    }

    public function initialize(): void {
        \gcc_jit_block_add_assignment(
            $this->context->initBlock,
            null,
            $this->size,
            $this->sizeof($this->context->getTypeFromString('__object__'))
        );
    }

    public function allocate(
        int $classId
    ): \gcc_jit_rvalue_ptr {
        return $this->context->helper->call(
            '__object__alloc',
            $this->context->constantFromInteger($classId),
            $this->context->type->class->getSize($classId)
        );

    }

}