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
    const TYPE_INFO_NONREFCOUNTED = 0;
    const TYPE_INFO_REFCOUNTED = 1;

    private \gcc_jit_struct_ptr $struct;
    private \gcc_jit_struct_ptr $virtualStruct;

    public \gcc_jit_type_ptr $type;
    private \gcc_jit_type_ptr $pointer;
    public \gcc_jit_type_ptr $virtualType;
    
    private array $fields;
    private \gcc_jit_field_ptr $virtualField;

    public function register(): void {
        $this->fields = [
            'refcount' => $this->context->helper->createField('refcount', 'int'),
            'typeinfo' => $this->context->helper->createField('typeinfo', 'int')
        ];
        $this->struct = \gcc_jit_context_new_struct_type(
            $this->context->context,
            null,
            '__ref__',
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->fields))
        );
        $this->type = \gcc_jit_struct_as_type($this->struct);
        $this->context->registerType(
            '__ref__',
            $this->type
        );
        $this->virtualField = $this->context->helper->createField(
            '__ref__count', 
            '__ref__'
        );
        $this->virtualStruct = \gcc_jit_context_new_struct_type(
            $this->context->context,
            null,
            '__ref__virtual',
            1,
            \gcc_jit_field_ptr_ptr::fromArray($this->virtualField)
        );
        $this->pointer = \gcc_jit_type_get_pointer(
            \gcc_jit_struct_as_type($this->virtualStruct)
        );
        $this->context->registerType(
            '__ref__virtual*',
            $this->pointer
        );
        $this->context->registerFunction(
            '__ref__init',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__ref__init',
                'void',
                false,
                'int',
                '__ref__virtual*'
            )
        );
        $this->context->registerFunction(
            '__ref__addref',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__ref__addref',
                'void',
                false,
                '__ref__virtual*'
            )
        );
        $this->context->registerFunction(
            '__ref__delref',
            $this->context->helper->createFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__ref__delref',
                'void',
                false,
                '__ref__virtual*'
            )
        );
    }

    public function implement(): void {
        $this->implementInit();
        $this->implementAddref();
        $this->implementDelref();
    }

    private function implementInit(): void {
        $init = $this->context->lookupFunction('__ref__init');
        $typeinfo = $init->params[0]->asRValue();
        $virtual = $init->params[1]->asRValue();
        $block = \gcc_jit_function_new_block($init->func, "main");
        $ref = \gcc_jit_rvalue_dereference_field($virtual, null, $this->virtualField);
        $this->context->helper->assign(
            $block,
            gcc_jit_lvalue_access_field($ref, null, $this->fields['refcount']),
            $this->context->helper->cast($this->context->constantFromInteger(1), 'int')
        );
        $this->context->helper->assign(
            $block,
            gcc_jit_lvalue_access_field($ref, null, $this->fields['typeinfo']),
            $typeinfo
        );
        \gcc_jit_block_end_with_void_return($block, null);
    } 

    private function implementAddref(): void {
        $addref = $this->context->lookupFunction('__ref__addref');
        $virtual = $addref->params[0]->asRValue();

        $block = \gcc_jit_function_new_block($addref->func, "main");
        $refcounted = \gcc_jit_function_new_block(
            $addref->func, 
            "refcounted"
        );
        $notrefcounted = \gcc_jit_function_new_block(
            $addref->func,
            "notcounted"
        );
        
        $ref = \gcc_jit_rvalue_dereference_field($virtual, null, $this->virtualField);
        $isRefCounted = $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_BITWISE_AND,
            'int',
            gcc_jit_rvalue_access_field($ref->asRValue(), null, $this->fields['typeinfo']),
            $this->context->constantFromInteger(self::TYPE_INFO_REFCOUNTED, 'int')
        );
        gcc_jit_block_end_with_conditional(
            $block,
            $this->context->location(),
            $this->context->helper->cast(
                $isRefCounted,
                'bool'
            ),
            $refcounted,
            $notrefcounted
        );
        gcc_jit_block_add_assignment_op(
            $refcounted,
            $this->context->location(),
            gcc_jit_lvalue_access_field($ref, null, $this->fields['refcount']),
            \GCC_JIT_BINARY_OP_PLUS,
            $this->context->constantFromInteger(1, 'int')
        );
        \gcc_jit_block_end_with_void_return(
            $refcounted, 
            $this->context->location()
        );
        \gcc_jit_block_end_with_void_return(
            $notrefcounted,
            $this->context->location()
        );
    }

    private function implementDelref(): void {
        $delref = $this->context->lookupFunction('__ref__delref');
        $virtual = $delref->params[0]->asRValue();

        $block = \gcc_jit_function_new_block($delref->func, "main");
        $refcounted = \gcc_jit_function_new_block(
            $delref->func, 
            "refcounted"
        );
        $needsfree = \gcc_jit_function_new_block(
            $delref->func,
            'needsfree'
        );
        $return = \gcc_jit_function_new_block(
            $delref->func,
            "notcounted"
        );
        
        $ref = \gcc_jit_rvalue_dereference_field($virtual, null, $this->virtualField);
        $isRefCounted = $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_BITWISE_AND,
            'int',
            gcc_jit_rvalue_access_field($ref->asRValue(), null, $this->fields['typeinfo']),
            $this->context->constantFromInteger(self::TYPE_INFO_REFCOUNTED, 'int')
        );
        gcc_jit_block_end_with_conditional(
            $block,
            $this->context->location(),
            $this->context->helper->cast(
                $isRefCounted,
                'bool'
            ),
            $refcounted,
            $return
        );
        gcc_jit_block_add_assignment_op(
            $refcounted,
            $this->context->location(),
            gcc_jit_lvalue_access_field($ref, null, $this->fields['refcount']),
            \GCC_JIT_BINARY_OP_MINUS,
            $this->context->constantFromInteger(1, 'int')
        );
        gcc_jit_block_end_with_conditional(
            $refcounted,
            $this->context->location(),
            gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                GCC_JIT_COMPARISON_LE,
                gcc_jit_rvalue_access_field(
                    $ref->asRValue(), 
                    $this->context->location(), 
                    $this->fields['refcount']
                ),
                $this->context->constantFromInteger(0, 'int')
            ),
            $needsfree,
            $return
        );
        $this->context->helper->eval(
            $needsfree,
            $this->context->memory->efree($virtual)
        );
        \gcc_jit_block_end_with_void_return(
            $needsfree,
            $this->context->location()
        );
        \gcc_jit_block_end_with_void_return(
            $return,
            $this->context->location()
        );
    }

    public function asField(): \gcc_jit_field_ptr {
        return $this->context->helper->createField(
            '__ref__count', 
            '__ref__'
        );
    }

    public function disableRefcount(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $value): void {
        $this->init($block, $value, self::TYPE_INFO_NONREFCOUNTED);
    }

    public function init(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $value, int $typeinfo = 0): void {
        $this->context->helper->eval(
            $block,
            $this->context->helper->call(
                '__ref__init',
                $this->context->helper->cast(
                    $this->context->constantFromInteger($typeinfo),
                    'int'
                ),
                $this->context->helper->cast(
                    $value,
                    '__ref__virtual*'
                )
            )
        );
    }

    public function addref(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $value): void {
        $this->context->helper->eval(
            $block,
            $this->context->helper->call(
                '__ref__addref',
                $this->context->helper->cast(
                    $value,
                    '__ref__virtual*'
                )
            )
        );
    }

    public function delref(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $value): void {
        $this->context->helper->eval(
            $block,
            $this->context->helper->call(
                '__ref__delref',
                $this->context->helper->cast(
                    $value,
                    '__ref__virtual*'
                )
            )
        );
    }
}