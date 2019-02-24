<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT\Builtin\Type;

use PHPCompiler\Backend\VM\JIT\Builtin\Type;
use PHPCompiler\Backend\VM\JIT\Builtin\Refcount;
use PHPCompiler\Backend\VM\JIT\Builtin;
use PHPCompiler\Backend\VM\JIT\Context;
use PHPCompiler\Backend\VM\JIT\Func;
use PHPCompiler\Backend\VM\JIT;

class String_ extends Type {
    private \gcc_jit_struct_ptr $struct;
    private \gcc_jit_type_ptr $pointer;
    private \gcc_jit_lvalue_ptr $size;
    private array $fields;

    public function register(): void {
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
        $this->context->registerFunction(
            '__string__alloc',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__string__alloc',
                '__string__*',
                false,
                'size_t'
            )
        );
        $this->context->registerFunction(
            'strlen',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_IMPORTED,
                'strlen',
                'size_t',
                false,
                'const char*'
            )
        );
    }

    public function implement(): void {
        $this->size = \gcc_jit_context_new_global(
            $this->context->context,
            null,
            \GCC_JIT_GLOBAL_INTERNAL,
            $this->context->getTypeFromString('size_t'),
            '__string__size'
        );
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

        $this->implementAlloc();
    }

    private function implementAlloc(): void {
        $alloc = $this->context->lookupFunction('__string__alloc');
        $block = \gcc_jit_function_new_block($alloc->func, 'main');
        $allocSize = $this->context->helper->binaryOp(
            GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $this->size->asRValue(),
            $alloc->params[0]->asRValue()
        );
        $local = \gcc_jit_function_new_local($alloc->func, null, $this->pointer, 'result');
        $this->context->helper->assign(
            $block, 
            $local,
            $this->context->memory->emalloc($allocSize, $this->pointer) 
        );
        $this->context->helper->assign(
            $block,
            $this->writeSize($local->asRValue()),
            $alloc->params[0]->asRValue()
        );
        $this->context->refcount->init(
            $block, 
            $local->asRValue(),
            Refcount::TYPE_INFO_REFCOUNTED
        );
        \gcc_jit_block_end_with_return($block,  null, $local->asRValue());
    }


    public function readSize(\gcc_jit_rvalue_ptr $struct): \gcc_jit_rvalue_ptr {
        return gcc_jit_rvalue_access_field(
            $struct,
            null,
            $this->fields['size']
        );
    }

    public function readValue(\gcc_jit_rvalue_ptr $struct): \gcc_jit_rvalue_ptr {
        return $this->context->helper->cast(gcc_jit_rvalue_access_field(
            $struct,
            null,
            $this->fields['value']
        ), 'char*');
    }

    public function writeSize(\gcc_jit_rvalue_ptr $pointer): \gcc_jit_lvalue_ptr {
        return gcc_jit_rvalue_dereference_field(
            $pointer,
            null,
            $this->fields['size']
        );
    }


    public function writeValue(\gcc_jit_rvalue_ptr $pointer): \gcc_jit_lvalue_ptr {
        return gcc_jit_rvalue_dereference_field(
            $pointer,
            null,
            $this->fields['value']
        );
    }

    public function sizePtr(\gcc_jit_rvalue_ptr $ptr): \gcc_jit_lvalue_ptr {
        return \gcc_jit_rvalue_dereference_field($ptr, null, $this->fields['size']);
    }
    public function valuePtr(\gcc_jit_rvalue_ptr $ptr): \gcc_jit_rvalue_ptr {
        return $this->context->helper->cast(
            \gcc_jit_lvalue_get_address(
                \gcc_jit_context_new_array_access(
                    $this->context->context,
                    $this->context->location(),
                    \gcc_jit_rvalue_dereference_field($ptr, null, $this->fields['value'])->asRValue(),
                    $this->context->constantFromInteger(0, 'size_t')
                ),
                $this->context->location()
            ),
            'char*'
        );
    }

    public function initialize(): void {
        \gcc_jit_block_add_assignment(
            $this->context->initBlock,
            null,
            $this->size,
            $this->sizeof($this->context->getTypeFromString('__string__'))
        );
    }

    private static $constId = 0;
    public function allocate(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $dest, 
        \gcc_jit_rvalue_ptr $length
    ): void {
        $this->context->helper->assign(
            $block,
            $dest,
            $this->context->helper->call(
                '__string__alloc',
                $length
            )
        );
    }

    public function reallocate(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $original, 
        \gcc_jit_rvalue_ptr $length
    ): void {
        $this->context->helper->assign(
            $block,
            $original,
            $this->context->helper->call(
                '__string__realloc',
                $original,
                $length
            )
        );
    }

    public function isString(\gcc_jit_rvalue_ptr $value): bool {
        return gcc_jit_rvalue_get_type($value)->equals($this->pointer);
    }

    public function toSizeRValue(\gcc_jit_rvalue_ptr $value): \gcc_jit_rvalue_ptr {
        $type = gcc_jit_rvalue_get_type($value);
        if ($type->equals($this->pointer)) {
            return $this->sizePtr($value)->asRValue();
        } elseif ($type->equals($this->context->getTypeFromString('char*')) || $type->equals($this->context->getTypeFromString('const char*'))) {
            return $this->context->helper->call(
                'strlen',
                $value
            );
        } else {
            throw new \LogicException("Not implemented support for string type " . $this->context->getStringFromType($type));
        }
    }

    public function toValueRValue(\gcc_jit_rvalue_ptr $value): \gcc_jit_rvalue_ptr {
        $type = gcc_jit_rvalue_get_type($value);
        if ($type->equals($this->pointer)) {
            return $this->valuePtr($value);
        } elseif ($type->equals($this->context->getTypeFromString('char*')) || $type->equals($this->context->getTypeFromString('const char*'))) {
            return $value;
        } else {
            throw new \LogicException("Not implemented support for string type " . $this->context->getStringFromType($type));
        }
    }

}