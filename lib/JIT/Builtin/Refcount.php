<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

class Refcount extends Builtin {
    const TYPE_INFO_NONREFCOUNTED     = 0b0000000000;
    const TYPE_INFO_REFCOUNTED        = 0b0000000001;

    const TYPE_INFO_TYPEMASK          = 0b1111111100;
    const TYPE_INFO_TYPE_STRING       = 0b0000000100;
    const TYPE_INFO_TYPE_OBJECT       = 0b0000001000;
    const TYPE_INFO_TYPE_MASKED_ARRAY = 0b0000001100;

    private \gcc_jit_struct_ptr $struct;
    private \gcc_jit_struct_ptr $virtualStruct;
    private \gcc_jit_field_ptr $virtualField;

    public \gcc_jit_type_ptr $type;
    public \gcc_jit_type_ptr $pointer;
    
    private array $fields;

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
        $this->context->registerType(
            '__ref__virtual**',
            \gcc_jit_type_get_pointer($this->pointer)
        );
        $this->context->registerFunction(
            '__ref__init',
            $this->context->helper->createNativeFunction(
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
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__ref__addref',
                'void',
                false,
                '__ref__virtual*'
            )
        );
        $this->context->registerFunction(
            '__ref__delref',
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__ref__delref',
                'void',
                false,
                '__ref__virtual*'
            )
        );
        $this->context->registerFunction(
            '__ref__separate',
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__ref__separate',
                'void',
                false,
                '__ref__virtual**'
            )
        );
    }

    public function implement(): void {
        $this->implementInit();
        $this->implementAddref();
        $this->implementDelref();
        $this->implementSeparate();
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
        $notnull = \gcc_jit_function_new_block($delref->func, "not_null");
        $refcounted = \gcc_jit_function_new_block($delref->func, "refcounted");
        $needsfree = \gcc_jit_function_new_block($delref->func,'needsfree');
        $return = \gcc_jit_function_new_block($delref->func,"notcounted");

        gcc_jit_block_end_with_conditional(
            $block,
            $this->context->location(),
            gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                \GCC_JIT_COMPARISON_EQ,
                $virtual,
                \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('__ref__virtual*'))
            ),
            $return,
            $notnull
        );
        
        $ref = \gcc_jit_rvalue_dereference_field($virtual, null, $this->virtualField);
        $isRefCounted = $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_BITWISE_AND,
            'int',
            gcc_jit_rvalue_access_field($ref->asRValue(), null, $this->fields['typeinfo']),
            $this->context->constantFromInteger(self::TYPE_INFO_REFCOUNTED, 'int')
        );
        gcc_jit_block_end_with_conditional(
            $notnull,
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
        $this->context->memory->free($needsfree, $virtual);
        \gcc_jit_block_end_with_void_return(
            $needsfree,
            $this->context->location()
        );
        \gcc_jit_block_end_with_void_return(
            $return,
            $this->context->location()
        );
    }

    private function implementSeparate(): void {
        $func = $this->context->lookupFunction('__ref__separate');
        $virtual = $func->params[0]->asRValue();

        $block = \gcc_jit_function_new_block($func->func, "main");
        $notnull = \gcc_jit_function_new_block($func->func, "not_null");
        $refcounted = \gcc_jit_function_new_block($func->func, "refcounted");
        $allocate = \gcc_jit_function_new_block($func->func, "copy_and_allocate");
        $shortcircuit = \gcc_jit_function_new_block($func->func, "shortcircuit");
        $delref = \gcc_jit_function_new_block($func->func, "delref");
        
        $ptr = \gcc_jit_rvalue_dereference($virtual, $this->context->location());

        gcc_jit_block_end_with_conditional(
            $block,
            $this->context->location(),
            gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                \GCC_JIT_COMPARISON_EQ,
                $ptr->asRValue(),
                \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('__ref__virtual*'))
            ),
            $shortcircuit,
            $notnull
        );


        $ref = \gcc_jit_rvalue_dereference_field(
            $ptr->asRValue(), 
            $this->context->location(), 
            $this->virtualField
        );

        $isRefCounted = $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_BITWISE_AND,
            'int',
            gcc_jit_rvalue_access_field($ref->asRValue(), null, $this->fields['typeinfo']),
            $this->context->constantFromInteger(self::TYPE_INFO_REFCOUNTED, 'int')
        );
        gcc_jit_block_end_with_conditional(
            $notnull,
            $this->context->location(),
            $this->context->helper->cast(
                $isRefCounted,
                'bool'
            ),
            $refcounted,
            $allocate
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
                $this->context->constantFromInteger(1, 'int')
            ),
            $shortcircuit,
            $delref
        );
        $this->delref($delref, $ptr->asRValue());
        \gcc_jit_block_end_with_jump($delref, $this->context->location(), $allocate);

        \gcc_jit_block_end_with_void_return(
            $shortcircuit,
            $this->context->location()
        );

        $type = $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_BITWISE_AND,
            'int',
            gcc_jit_rvalue_access_field($ref->asRValue(), null, $this->fields['typeinfo']),
            $this->context->constantFromInteger(self::TYPE_INFO_TYPEMASK, 'int')
        );
        $default = \gcc_jit_function_new_block($func->func, "default");
        \gcc_jit_block_end_with_void_return(
            $default,
            $this->context->location()
        );
        $stringBlock = \gcc_jit_function_new_block($func->func, "string_block");
        $this->context->helper->eval($stringBlock, $this->context->helper->call(
            '__string__separate',
            $virtual
        ));
        \gcc_jit_block_end_with_void_return(
            $stringBlock,
            $this->context->location()
        );
        $typeInfoTypeString = \gcc_jit_context_new_rvalue_from_long($this->context->context, $this->context->getTypeFromString('int'), self::TYPE_INFO_TYPE_STRING);
        \gcc_jit_block_end_with_switch(
            $allocate,
            $this->context->location(),
            $type,
            $default,
            1,
            \gcc_jit_case_ptr_ptr::fromArray(
                \gcc_jit_context_new_case(
                    $this->context->context,
                    $typeInfoTypeString,
                    $typeInfoTypeString,
                    $stringBlock
                )
            )
        );
    }

    public function asField(): \gcc_jit_field_ptr {
        return $this->context->helper->createField(
            '__ref__count', 
            '__ref__'
        );
    }

    public function disableRefcount(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $value): void {
        $ref = \gcc_jit_rvalue_dereference_field(
            $this->context->helper->cast(
                $value,
                '__ref__virtual*'
            ), 
            $this->context->location(), 
            $this->virtualField
        );
        \gcc_jit_block_add_assignment_op(
            $block,
            $this->context->location(),
            gcc_jit_lvalue_access_field($ref, null, $this->fields['typeinfo']),
            \GCC_JIT_BINARY_OP_BITWISE_AND,
            $this->context->constantFromInteger(~self::TYPE_INFO_REFCOUNTED, 'int')
        );
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

    public function separate(\gcc_jit_block_ptr $block, \gcc_jit_lvalue_ptr $value): void {
        $this->context->helper->eval(
            $block,
            $this->context->helper->call(
                '__ref__separate',
                $this->context->helper->cast(
                    \gcc_jit_lvalue_get_address($value, $this->context->location()),
                    '__ref__virtual**'
                )
            )
        );
    }
}