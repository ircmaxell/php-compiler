<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\Type;

use PHPCfg\Operand;
use PHPCfg\Operand\Literal;
use PHPCompiler\JIT\Builtin\Type;
use PHPCompiler\JIT\Builtin\Refcount;
use PHPCompiler\JIT\Variable;
use PHPCompiler\JIT\Func;

class MaskedArray extends Type {
    private \gcc_jit_struct_ptr $struct;
    private \gcc_jit_lvalue_ptr $size;

    protected array $fields;

    public function register(): void {
        $this->struct = \gcc_jit_context_new_opaque_struct(
            $this->context->context,
            null,
            '__maskedarray__'
        );
        $this->context->registerType(
            '__maskedarray__',
            \gcc_jit_struct_as_type($this->struct)
        );
        $this->pointer = $this->context->getTypeFromString('__maskedarray__*');

        $this->context->registerFunction(
            '__maskedarray__alloc',
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__maskedarray__alloc',
                '__maskedarray__*',
                false,
                'size_t'
            )
        );
        $this->context->registerFunction(
            '__maskedarray__grow',
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__maskedarray__realloc',
                '__maskedarray__*',
                false,
                '__maskedarray__*'
            )
        );
    }

    public function implement(): void {
        $this->size = \gcc_jit_context_new_global(
            $this->context->context,
            null,
            \GCC_JIT_GLOBAL_INTERNAL,
            $this->context->getTypeFromString('size_t'),
            '__maskedarray__size'
        );
        $this->fields = [
            'refcount' => $this->context->refcount->asField('refcount'),
            'size' => $this->context->helper->createField('size', 'size_t'),
            'mask' => $this->context->helper->createField('mask', 'size_t'),
            'data' => $this->context->helper->createField('data', '__value__*'),
        ];
        \gcc_jit_struct_set_fields(
            $this->struct,
            null,
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->fields))
        );
        $this->implementAlloc();
        $this->implementGrow();
    }

    private function computeAllocSize(\gcc_jit_rvalue_ptr $size): \gcc_jit_rvalue_ptr {
        return $this->context->helper->binaryOp(
            GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $this->size->asRValue(),
            $this->context->helper->binaryOp(
                GCC_JIT_BINARY_OP_MULT,
                'size_t',
                $size,
                // sizeof __value__*
                $this->context->constantFromInteger(8, 'size_t') 
            )
        );
    }

    private function implementAlloc(): void {
        $alloc = $this->context->lookupFunction('__maskedarray__alloc');
        $block = \gcc_jit_function_new_block($alloc->func, 'main');
        $size = $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_LSHIFT,
            'size_t',
            $this->context->constantFromInteger(1, 'size_t'),
            $alloc->params[0]->asRValue()
        );
        $allocSize = $this->computeAllocSize($size);
        $local = \gcc_jit_function_new_local($alloc->func, null, $this->pointer, 'result');
        $this->context->helper->assign(
            $block, 
            $local,
            $this->context->memory->malloc($allocSize, $this->pointer) 
        );
        $this->context->helper->assign(
            $block,
            $this->writeField('size', $local->asRValue()),
            $size
        );
        $this->context->helper->assign(
            $block,
            $this->writeField('mask', $local->asRValue()),
            $this->context->helper->binaryOp(
                \GCC_JIT_BINARY_OP_MINUS,
                'size_t',
                $size,
                $this->context->constantFromInteger(1, 'size_t')
            )
        );
        $this->context->refcount->init(
            $block, 
            $local->asRValue(),
            Refcount::TYPE_INFO_REFCOUNTED | Refcount::TYPE_INFO_TYPE_STRING
        );
        \gcc_jit_block_end_with_return($block,  null, $local->asRValue());
    }

    private function implementGrow(): void {
        $grow = $this->context->lookupFunction('__maskedarray__grow');
        $block = \gcc_jit_function_new_block($grow->func, 'main');
        $size = $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_LSHIFT,
            'size_t',
            $this->readField('size', $grow->params[0]->asRValue()),
            $this->context->constantFromInteger(1, 'size_t')
        );
        $allocSize = $this->computeAllocSize($size);
        $local = \gcc_jit_function_new_local($grow->func, null, $this->pointer, 'result');
        $this->context->helper->assign(
            $block, 
            $local,
            $this->context->memory->realloc($grow->params[0]->asRValue(), $allocSize, $this->pointer) 
        );
        $this->context->helper->assign(
            $block,
            $this->writeField('size', $local->asRValue()),
            $size
        );
        $this->context->helper->assign(
            $block,
            $this->writeField('mask', $local->asRValue()),
            $this->context->helper->binaryOp(
                \GCC_JIT_BINARY_OP_MINUS,
                'size_t',
                $size,
                $this->context->constantFromInteger(1, 'size_t')
            )
        );
        \gcc_jit_block_end_with_return($block,  null, $local->asRValue());
    }

    public function initialize(): void {
        \gcc_jit_block_add_assignment(
            $this->context->initBlock,
            null,
            $this->size,
            $this->sizeof($this->context->getTypeFromString('__maskedarray__'))
        );
    }

    public function allocate(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $dest, 
        \gcc_jit_rvalue_ptr $size
    ): void {
        $this->context->helper->assign(
            $block,
            $dest,
            $this->context->helper->call(
                '__maskedarray__alloc',
                $length
            )
        );
    }

    public function grow(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $dest
    ): void {
        $this->context->helper->assign(
            $block,
            $dest,
            $this->context->helper->call(
                '__maskedarray__grow',
                $dest->asRValue()
            )
        );
    }

    public function read(\gcc_jit_rvalue_ptr $ptr, \gcc_jit_rvalue_ptr $offset): \gcc_jit_rvalue_ptr {
        $data = $this->readField('data', $ptr);
        return \gcc_jit_context_new_array_access(
            $this->context->context,
            $this->context->location(),
            $data,
            $this->context->helper->binaryOp(
                \GCC_JIT_BINARY_OP_BITWISE_AND,
                'size_t',
                $offset,
                $this->readField('mask', $ptr)
            )
        )->asRValue();
    }

    public function write(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $ptr, \gcc_jit_rvalue_ptr $offset, \gcc_jit_rvalue_ptr $value) {
        $data = $this->readField('data', $ptr);
        $lvalue = \gcc_jit_context_new_array_access(
            $this->context->context,
            $this->context->location(),
            $data,
            $this->context->helper->binaryOp(
                \GCC_JIT_BINARY_OP_BITWISE_AND,
                'size_t',
                $offset,
                $this->readField('mask', $ptr)
            )
        );
        $this->context->helper->assign(
            $block,
            $lvalue,
            $value
        );
    }

    public function size(\gcc_jit_rvalue_ptr $ptr): \gcc_jit_rvalue_ptr {
        return $this->readField('size', $ptr);
    }
    

}