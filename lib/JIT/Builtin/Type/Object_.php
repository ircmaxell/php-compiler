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

class Object_ extends Type {
    private \gcc_jit_struct_ptr $struct;
    public \gcc_jit_type_ptr $pointer;
    private \gcc_jit_lvalue_ptr $size;
    protected array $fields;
    private array $classes = [];
    private array $properties = [];
    private array $propNameMap = [];

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
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__object__alloc',
                '__object__*',
                false,
                'long long',
                'size_t'
            )
        );
        $this->context->registerFunction(
            '__object__propfetch',
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__object__propfetch',
                'void*',
                false,
                '__object__*',
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
        $return = \gcc_jit_function_new_block($alloc->func, 'return');
        $conditional = \gcc_jit_function_new_block($alloc->func, 'cond');
        $loop = \gcc_jit_function_new_block($alloc->func, 'loop');
        $local = \gcc_jit_function_new_local($alloc->func, null, $this->pointer, 'result');
        $allocSize = $this->context->helper->binaryOp(
            GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $this->size->asRValue(),
            $this->context->helper->binaryOp(
                GCC_JIT_BINARY_OP_MULT,
                'size_t',
                $alloc->params[1]->asRValue(),
                $this->context->constantFromInteger(8, 'size_t')
            )
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
        \gcc_jit_block_end_with_conditional(
            $conditional,
            $this->context->location(),
            \gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                \GCC_JIT_COMPARISON_GT,
                $alloc->params[1]->asRValue(),
                $this->context->constantFromInteger(0, 'size_t')
            ),
            $loop,
            $return
        );
        //todo: initialize pointers
        $cast = $this->context->helper->cast($local->asRValue(), 'char*');
        $property = \gcc_jit_lvalue_get_address(   
            \gcc_jit_context_new_array_access(
                $this->context->context,
                $this->context->location(),
                $cast,
                $this->context->helper->binaryOp(
                    GCC_JIT_BINARY_OP_PLUS,
                    'size_t',
                    $this->size->asRValue(),
                    $this->context->helper->binaryOp(
                        \GCC_JIT_BINARY_OP_MULT,
                        'size_t',
                        $this->context->constantFromInteger(8, 'size_t'),
                        $alloc->params[1]->asRValue()
                    )
                )
            ),
            $this->context->location()
        );
        \gcc_jit_block_add_assignment(
            $loop,
            $this->context->location(),
            \gcc_jit_context_new_array_access(
                $this->context->context,
                $this->context->location(),
                \gcc_jit_context_new_cast(
                    $this->context->context,
                    $this->context->location(),
                    $property,
                    $this->context->getTypeFromString('void**')
                ),
                $this->context->constantFromInteger(0, 'size_t')
            ),
            \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('void*'))
        );

        \gcc_jit_block_add_assignment_op(
            $loop, 
            $this->context->location(),
            $alloc->params[1]->asLValue(),
            \GCC_JIT_BINARY_OP_MINUS,
            $this->context->constantFromInteger(1, 'size_t')
        );
        \gcc_jit_block_end_with_jump($block, $this->context->location(), $conditional);
        \gcc_jit_block_end_with_jump($loop, $this->context->location(), $conditional);
        \gcc_jit_block_end_with_return($return,  null, $local->asRValue());
    }

    public function initialize(): void {
        \gcc_jit_block_add_assignment(
            $this->context->initBlock,
            null,
            $this->size,
            $this->sizeof($this->context->getTypeFromString('__object__'))
        );
    }

    public function shutdown(): void {
        $this->implementPropFetch();        
    }

    private function implementPropFetch(): void {
        $fetch = $this->context->lookupFunction('__object__propfetch');
        $block = \gcc_jit_function_new_block($fetch->func, 'main');
        $defaultBlock = \gcc_jit_function_new_block($fetch->func, 'default_');
        \gcc_jit_block_end_with_return(
            $defaultBlock,
            $this->context->location(), 
            \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('void*'))
        );
        $cases = [];
        foreach ($this->properties as $classId => $properties) {
            $classBlock = \gcc_jit_function_new_block($fetch->func, 'class_' . $classId);
            $constId = $this->context->constantFromInteger($classId);
            $cases[] = \gcc_jit_context_new_case(
                $this->context->context,
                $constId,
                $constId,
                $classBlock
            );
            $this->implementPropFetchForPropset($fetch->func, $classBlock, $fetch->params[0]->asRValue(), $fetch->params[1]->asRValue(), $classId, $properties)
;        }
        \gcc_jit_block_end_with_switch(
            $block,
            $this->context->location(),
            $this->readField('class_id', $fetch->params[0]->asRValue()),
            $defaultBlock,
            count($cases),
            \gcc_jit_case_ptr_ptr::fromArray(...$cases)
        );
    }

    private function implementPropFetchForPropset(\gcc_jit_function_ptr $func, \gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $obj, \gcc_jit_rvalue_ptr $param, int $classId, array $properties): void {
        $cast = $this->context->helper->cast($obj, 'char*');
        $defaultBlock = \gcc_jit_function_new_block($func, 'default_' . $classId);
        \gcc_jit_block_end_with_return(
            $defaultBlock,
            $this->context->location(), 
            \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('void*'))
        );
        $cases = [];
        foreach ($properties as $prop) {
            $propBlock = \gcc_jit_function_new_block($func, 'class_' . $classId . '_' . $prop[0]);
            \gcc_jit_block_end_with_return(
                $propBlock,
                $this->context->location(),
                $this->context->helper->cast(
                    \gcc_jit_lvalue_get_address(
                        gcc_jit_context_new_array_access(
                            $this->context->context,
                            $this->context->location(),
                            $cast,
                            $this->context->helper->binaryOp(
                                GCC_JIT_BINARY_OP_PLUS,
                                'size_t',
                                $this->size->asRValue(),
                                $this->context->constantFromInteger(8 * $prop[3], 'size_t')
                            )
                        ),
                        $this->context->location()
                    ),
                    'void*'
                )
            );
            $offset = $prop[3];
            $constId = \gcc_jit_context_new_rvalue_from_long(
                $this->context->context,
                $this->context->getTypeFromString('size_t'),
                $prop[0]
            );
            $cases[] = \gcc_jit_context_new_case(
                $this->context->context,
                $constId,
                $constId,
                $propBlock
            );
        }
        \gcc_jit_block_end_with_switch(
            $block,
            $this->context->location(),
            $param,
            $defaultBlock,
            count($cases),
            \gcc_jit_case_ptr_ptr::fromArray(...$cases)
        );
    }

    public function allocate(
        int $classId
    ): \gcc_jit_rvalue_ptr {
        return $this->context->helper->call(
            '__object__alloc',
            $this->context->constantFromInteger($classId),
            $this->getSize($classId)
        );

    }

    public function declareClass(Operand $name): int {
        if (!$name instanceof Literal) {
            throw new \LogicException('JIT only supports constant named classes');
        }
        $id = count($this->classes);
        $this->properties[$id] = [];
        return $this->classes[strtolower($name->value)] = $id;
    }

    public function getSize(int $classId): \gcc_jit_rvalue_ptr {
        return $this->context->constantFromInteger(count($this->properties[$classId]), 'size_t');
    }

    public function lookupOperand(Operand $name): int {
        if (!$name instanceof Literal) {
            
            throw new \LogicException('JIT only supports constant named classes');
        }
        return $this->lookup($name->value);
    }

    public function lookup(string $name): int {
        $lcname = strtolower($name);
        if (!isset($this->classes[$lcname])) {
            throw new \LogicException("Unknown class lookup: $name");
        }
        return $this->classes[$lcname];
    }

    public function defineProperty(int $classId, string $name, int $type): void {
        if (!isset($this->propNameMap[$name])) {
            $this->propNameMap[$name] = count($this->propNameMap);
        }
        $this->properties[$classId][] = [
            $this->propNameMap[$name], $name, $type, count($this->properties[$classId])
        ];
    }

    public function propertyFetch(\gcc_jit_rvalue_ptr $obj, string $class, string $name): Variable {
        if (!isset($this->propNameMap[$name])) {
            throw new \LogicException('Attempting to fetch unknown property');
        }
        $classId = $this->lookup($class);
        $nameId = $this->propNameMap[$name];
        foreach ($this->properties[$classId] as $propset) {
            if ($propset[0] === $nameId) {
                $prop = $this->fetchAndCast($obj, $nameId, $propset[2]);
                return new Variable(
                    $this->context,
                    $propset[2],
                    Variable::KIND_VARIABLE,
                    $prop->asRValue(),
                );
            }
        }
        throw new \LogicException("Could not find property $name for class $classId");
    }

    private function fetchAndCast(\gcc_jit_rvalue_ptr $obj, int $nameId, int $type): \gcc_jit_lvalue_ptr {
        $void = $this->context->helper->call(
            '__object__propfetch',
            $obj,
            $this->context->constantFromInteger($nameId, 'size_t')
        );
        $stringType = Variable::getStringType($type);
        return \gcc_jit_context_new_array_access(
            $this->context->context,
            $this->context->location(),
            \gcc_jit_context_new_cast(
                $this->context->context,
                $this->context->location(),
                $void,
                $this->context->getTypeFromString($stringType . '*')
            ),
            $this->context->constantFromInteger(0, 'size_t')
        );
    }


}