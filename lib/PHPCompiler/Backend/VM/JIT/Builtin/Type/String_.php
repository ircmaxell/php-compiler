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
use PHPCompiler\Backend\VM\JIT\Variable;
use PHPCompiler\Backend\VM\JIT;

class String_ extends Type {
    private \gcc_jit_struct_ptr $struct;
    public \gcc_jit_type_ptr $pointer;
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
            '__string__init',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__string__init',
                '__string__*',
                false,
                'const char*',
                'size_t'
            )
        );
        $this->context->registerFunction(
            '__string__realloc',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__string__realloc',
                '__string__*',
                false,
                '__string__*',
                'size_t'
            )
        );
        $this->context->registerFunction(
            '__string__separate',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__string__separate',
                'void',
                false,
                '__ref__virtual**'
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
        $this->context->registerFunction(
            '__string__strlen',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__string__strlen',
                'long long',
                false,
                '__string__*'
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
        $this->implementInit();
        $this->implementRealloc();
        $this->implementSeparate();
        $this->implementStrlen();
        $this->context->functions['strlen'] = $this->context->lookupFunction('__string__strlen')->func;
    }

    private function implementStrlen(): void {
        $strlen = $this->context->lookupFunction('__string__strlen');
        $block = \gcc_jit_function_new_block($strlen->func, 'main');
        \gcc_jit_block_end_with_return(
            $block,
            null,
            $this->context->helper->cast(
                $this->sizePtr($strlen->params[0]->asRValue())->asRValue(),
                'long long'
            )
        );
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
            $this->context->memory->malloc($allocSize, $this->pointer) 
        );
        $this->context->helper->assign(
            $block,
            $this->writeSize($local->asRValue()),
            $alloc->params[0]->asRValue()
        );
        $this->context->refcount->init(
            $block, 
            $local->asRValue(),
            Refcount::TYPE_INFO_REFCOUNTED | Refcount::TYPE_INFO_TYPE_STRING
        );
        \gcc_jit_block_end_with_return($block,  null, $local->asRValue());
    }

    private function implementInit(): void {
        $init = $this->context->lookupFunction('__string__init');
        $block = \gcc_jit_function_new_block($init->func, 'main');
        $local = \gcc_jit_function_new_local($init->func, null, $this->pointer, 'result');
        $this->context->helper->assign(
            $block,
            $local,
            $this->context->helper->call(
                '__string__alloc',
                $init->params[1]->asRValue()
            )
        );
        $this->context->memory->memcpy(
            $block,
            $this->valuePtr($local->asRValue()),
            $init->params[0]->asRValue(),
            $init->params[1]->asRValue()
        );
        \gcc_jit_block_end_with_return($block,  null, $local->asRValue());
    }

    private function implementRealloc(): void {
        $realloc = $this->context->lookupFunction('__string__realloc');
        $block = \gcc_jit_function_new_block($realloc->func, 'main');
        $isnull = \gcc_jit_function_new_block($realloc->func, 'is_null');
        $notnull = \gcc_jit_function_new_block($realloc->func, 'not_null');
        
        $ptr = $realloc->params[0]->asLValue();
        $reallocSize = $this->context->helper->binaryOp(
            GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $this->size->asRValue(),
            $realloc->params[1]->asRValue()
        );
        $local = \gcc_jit_function_new_local($realloc->func, null, $this->pointer, 'result');
        $this->context->helper->assign(
            $block, 
            $local,
            $this->context->memory->realloc($ptr->asRValue(), $reallocSize, $this->pointer) 
        );
        $this->context->helper->assign(
            $block,
            $this->writeSize($local->asRValue()),
            $realloc->params[1]->asRValue()
        );
        gcc_jit_block_end_with_conditional(
            $block,
            $this->context->location(),
            gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                \GCC_JIT_COMPARISON_EQ,
                $ptr->asRValue(),
                \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('__string__*'))
            ),
            $isnull,
            $notnull
        );
        $this->context->refcount->init($isnull, $local->asRValue(), Refcount::TYPE_INFO_REFCOUNTED | Refcount::TYPE_INFO_TYPE_STRING);

        \gcc_jit_block_end_with_jump($isnull,  null,$notnull);
        \gcc_jit_block_end_with_return($notnull,  null, $local->asRValue());
    }

    private function implementSeparate(): void {
        $func = $this->context->lookupFunction('__string__separate');
        $virtual = $func->params[0]->asRValue();
        $deref = \gcc_jit_rvalue_dereference($virtual, $this->context->location());
        $from = $this->context->helper->cast(
            $deref->asRValue(),
            '__string__*'
        );
        $block = \gcc_jit_function_new_block($func->func, 'main');
        $tmp = \gcc_jit_function_new_local($func->func, $this->context->location(), $this->pointer, 'tmp');
        $length = $this->sizePtr($from)->asRValue();
        $this->init(
            $block,
            $tmp,
            $this->context->helper->cast($this->valuePtr($from), 'const char*'),
            $length
        );
        \gcc_jit_block_add_assignment(
            $block,
            $this->context->location(),
            $deref,
            \gcc_jit_context_new_cast(
                $this->context->context,
                $this->context->location(),
                $tmp->asRValue(),
                $this->context->refcount->pointer
            )
        );
        \gcc_jit_block_end_with_void_return($block, $this->context->location());   
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
        \gcc_jit_rvalue_ptr $length,
        bool $isConstant = false
    ): void {
        $this->context->helper->assign(
            $block,
            $dest,
            $this->context->helper->call(
                '__string__alloc',
                $length
            )
        );
        if ($isConstant) {
            // disable refcount
            $this->context->refcount->disableRefcount($block, $dest->asRValue());
        }
    }

    public function init(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $dest,
        \gcc_jit_rvalue_ptr $value,
        \gcc_jit_rvalue_ptr $length,
        bool $isConstant = false
    ): void {
        $this->context->helper->assign(
            $block,
            $dest,
            $this->context->helper->call(
                '__string__init',
                $value,
                $length
            )
        );
        if ($isConstant) {
            // disable refcount
            $this->context->refcount->disableRefcount($block, $dest->asRValue());
        }
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
                $original->asRValue(),
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

    public function size(Variable $var): \gcc_jit_rvalue_ptr {
        switch ($var->type) {
            case Variable::TYPE_STRING:
                // pointer call
                return $this->sizePtr($var->rvalue)->asRValue();
            case Variable::TYPE_NATIVE_LONG:
                return $this->context->helper->cast(
                    $this->context->helper->call(
                        'snprintf',
                        \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('char*')),
                        $this->context->constantFromInteger(0, 'size_t'),
                        $this->context->constantFromString('%lld'),
                        $var->rvalue
                    ),
                    'size_t'
                );
        }
    }

    public function value(Variable $var): \gcc_jit_rvalue_ptr {
        switch ($var->type) {
            case Variable::TYPE_STRING:
                // pointer call
                return $this->valuePtr($var->rvalue);
        }
    }

    public function concat(\gcc_jit_block_ptr $block, Variable $dest, Variable $left, Variable $right): void {
        assert($dest->type === Variable::TYPE_STRING);
        $this->context->refcount->separate($block, $dest->lvalue);


        $leftSize = $this->size($left);
        $rightSize = $this->size($right);
        $this->reallocate($block, $dest->lvalue, $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $leftSize,
            $rightSize
        ));

        if ($left !== $dest) {
            $this->copy(
                $block, 
                $dest, 
                $left,
                $this->context->constantFromInteger(0, 'size_t')
            );
        }
        $this->copy(
            $block,
            $dest,
            $right,
            $this->context->helper->binaryOp(
                \GCC_JIT_BINARY_OP_MINUS,
                'size_t',
                $this->size($dest),
                $this->size($right)
            )
        );
        
    }

    private function copy(\gcc_jit_block_ptr $block, Variable $dest, Variable $other, \gcc_jit_rvalue_ptr $offset): void {
        $addr = \gcc_jit_lvalue_get_address(
            \gcc_jit_context_new_array_access(
                $this->context->context,
                $this->context->location(),
                $this->valuePtr($dest->rvalue),
                $offset
            ),
            $this->context->location()
        );
        switch ($other->type) {
            case Variable::TYPE_STRING:
                $this->context->memory->memcpy($block, $addr, $this->valuePtr($other->rvalue), $this->sizePtr($other->rvalue)->asRValue());
                break;
            case Variable::TYPE_NATIVE_LONG:
                $this->context->helper->eval(
                    $block,
                    $this->context->helper->call(
                        'sprintf',
                        $addr,
                        $this->context->constantFromString('%lld'),
                        $other->rvalue
                    )
                );
                break;
            default:
                throw new \LogicException("Unhandled type for copy $other->type");
        }
    }

}