<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Handler\Builtins\Functions;

use PHPCompiler\Handler\Builtins\Functions;
use PHPCompiler\Frame;
use PHPCompiler\VM\Variable as VMVariable;
use PHPCompiler\VM\ClassEntry;
use PHPTypes\Type;
use PHPCompiler\JIT\Context as JITContext;
use PHPCompiler\JIT\Builtin as JITBuiltin;
use PHPCompiler\JIT\Variable as JITVariable;
use PHPCompiler\JIT\Func as Func;

class VarDump extends Functions {

    private array $jitFunctions = [];

    public function getName(): string {
        return 'var_dump';
    }

    public function execute(Frame $frame): void {
        foreach ($frame->calledArgs as $arg) {
            $this->var_dump($arg, 1);
        }
    }

    private function var_dump(VMVariable $var, int $level) {
        if ($level > 1) {
            echo str_repeat(' ', $level - 1);
        }
restart:
        switch ($var->type) {
            case VMVariable::TYPE_INTEGER:
                printf("int(%d)\n", $var->toInt());
                break;
            case VMVariable::TYPE_FLOAT:
                printf("float(%G)\n", $var->toFloat());
                break;
            case VMVariable::TYPE_STRING:
                printf("string(%d) \"%s\"\n", strlen($var->toString()), $var->toString());
                break;
            case VMVariable::TYPE_BOOLEAN:
                printf("bool(%s)\n", $var->toBool() ? 'true' : 'false');
                break;
            case VMVariable::TYPE_OBJECT:
                $props = $var->object->getProperties(ClassEntry::PROP_PURPOSE_DEBUG);
                printf("object(%s)#%d (%d) {\n", $var->toObject()->class->name, $var->toObject()->id, count($props));
                foreach ($props as $key => $prop) {
                    $this->var_dump_object_property($key, $prop, $level);
                }
                if ($level > 1) {
                    echo str_repeat(' ', $level - 1);
                }
                echo "}\n";
                break;
            case VMVariable::TYPE_INDIRECT:
                $var = $var->resolveIndirect();
                goto restart;
            default:
                throw new \LogicException("var_dump not implemented for type");
        }
    }

    private function var_dump_object_property(string $key, VMVariable $prop, int $level) {
        echo str_repeat(' ', $level + 1);
        printf("[\"%s\"]=>\n", $key);
        $this->var_dump($prop, $level + 2);
    }

    public function registerJIT(JITContext $context): void {
        $this->jitContext = $context;
        switch ($context->loadType) {
            case JITBuiltin::LOAD_TYPE_EXPORT:
                $kind = \GCC_JIT_FUNCTION_EXPORTED;
                break;
            case JITBuiltin::LOAD_TYPE_IMPORT:
                $kind = \GCC_JIT_FUNCTION_IMPORTED;
                break;
            case JITBuiltin::LOAD_TYPE_EMBED:
            case JITBuiltin::LOAD_TYPE_STANDALONE:
                $kind = \GCC_JIT_FUNCTION_INTERNAL;
                break;
            default: 
                throw new \LogicException("Unknown load type: $context->loadType");
        }
        $lcname = strtolower($this->getName());
        $funcName = '__builtin__' . $lcname;
        $func = $this->jitContext->helper->createVarArgFunction(
            $kind,
            $funcName,
            $this->getReturnType(),
            '__value__'
        );
        $this->jitContext->registerFunction(
            $funcName,
            $func
        );
        if ($kind !== \GCC_JIT_FUNCTION_IMPORTED) {
            $this->implement($func);
        }
        $context->functions[$lcname] = $func;

    }

    public function getReturnType(): string {
        return 'void';
    }
    public function getParamTypes(): array {
        return [];
    }

    public function implement(Func $func): void {
        $internal = $this->implementInternal();
        $block = \gcc_jit_function_new_block($func->func, 'main');
        $next = \gcc_jit_function_new_block($func->func, 'next');
        $call = \gcc_jit_function_new_block($func->func, 'call');
        $end = \gcc_jit_function_new_block($func->func, 'end');
        $local = gcc_jit_function_new_local(
            $func->func, 
            $this->jitContext->location(), 
            $this->jitContext->getTypeFromString('size_t'),
            'i'
        );
        \gcc_jit_block_add_assignment($block, $this->jitContext->location(), $local, $this->jitContext->constantFromInteger(0, 'size_t'));
        \gcc_jit_block_end_with_jump($block, $this->jitContext->location(), $next);
        \gcc_jit_block_end_with_conditional(
            $next,
            $this->jitContext->location(),
            \gcc_jit_context_new_comparison(
                $this->jitContext->context,
                $this->jitContext->location(),
                \GCC_JIT_COMPARISON_LT,
                $local->asRValue(),
                $func->nargs->asRValue()
            ),
            $call,
            $end
        );
        
        $this->jitContext->helper->eval($call, \gcc_jit_context_new_call(
            $this->jitContext->context,
            $this->jitContext->location(),
            $internal,
            2,
            \gcc_jit_rvalue_ptr_ptr::fromArray(
                \gcc_jit_context_new_array_access(
                    $this->jitContext->context,
                    $this->jitContext->location(),
                    $func->varargs->asRValue(),
                    $local->asRValue()
                )->asRValue(),
                $this->jitContext->constantFromInteger(0, 'size_t')
            )
        ));
        \gcc_jit_block_add_assignment_op($call, $this->jitContext->location(), $local, \GCC_JIT_BINARY_OP_PLUS, $this->jitContext->constantFromInteger(1, 'size_t'));
        \gcc_jit_block_end_with_jump($call, $this->jitContext->location(), $next);

        \gcc_jit_block_end_with_void_return($end, $this->jitContext->location());
    }

    private function implementInternal(): \gcc_jit_function_ptr {
        $params = [
            \gcc_jit_context_new_param(
                $this->jitContext->context, 
                $this->jitContext->location(), 
                $this->jitContext->getTypeFromString('__value__'), 
                'value'
            ),
            \gcc_jit_context_new_param(
                $this->jitContext->context, 
                $this->jitContext->location(), 
                $this->jitContext->getTypeFromString('size_t'), 
                'indent'
            ),
        ];
        $func = \gcc_jit_context_new_function(
            $this->jitContext->context, 
            $this->jitContext->location(),
            \GCC_JIT_FUNCTION_ALWAYS_INLINE,
            $this->jitContext->getTypeFromString('void'),
            '__builtin__var_dump__internal',
            2, 
            \gcc_jit_param_ptr_ptr::fromArray(
                ...$params
            ),
            0
        );
        $block = \gcc_jit_function_new_block($func, 'main');
        $default = \gcc_jit_function_new_block($func, 'default_');
        $cases = [
            $this->implementSimple(JITVariable::TYPE_NATIVE_LONG, $func, $params[0], $params[1]),
            $this->implementSimple(JITVariable::TYPE_NATIVE_DOUBLE, $func, $params[0], $params[1]),
            $this->implementString($func, $params[0], $params[1]),
        ];
        \gcc_jit_block_end_with_switch(
            $block,
            $this->jitContext->location(),
            $this->jitContext->type->value->readType($params[0]->asRValue()),
            $default,
            count($cases),
            \gcc_jit_case_ptr_ptr::fromArray(...$cases)
        );
        $this->jitContext->helper->eval(
            $default,
            $this->jitContext->helper->call(
                'printf',
                $this->jitContext->constantFromString("%*sunknown()\n"),
                $params[1]->asRValue(),
                $this->jitContext->constantFromString(""),
            )
        );
        \gcc_jit_block_end_with_void_return($default, $this->jitContext->location());
        return $func;
    }

    private function implementSimple(int $type, \gcc_jit_function_ptr $func, \gcc_jit_param_ptr $obj, \gcc_jit_param_ptr $indent): \gcc_jit_case_ptr {
        switch ($type) {
            case JITVariable::TYPE_NATIVE_LONG:
                $format = "%*sint(%lld)\n";
                break;
            case JITVariable::TYPE_NATIVE_DOUBLE:
                $format = "%*sfloat(%G)\n";
                break;
            default:
                throw new \LogicException('Not implemented simple type');
        }
        $block = \gcc_jit_function_new_block($func, 'simple_' . $type);
        $this->jitContext->helper->eval(
            $block,
            $this->jitContext->helper->call(
                'printf',
                $this->jitContext->constantFromString($format),
                $indent->asRValue(),
                $this->jitContext->constantFromString(""),
                $this->jitContext->type->value->readValue($type, $obj->asRValue())
            )
        );
        \gcc_jit_block_end_with_void_return($block, $this->jitContext->location());
        $value = \gcc_jit_context_new_rvalue_from_long(
            $this->jitContext->context,
            $this->jitContext->getTypeFromString('unsigned char'),
            $type
        );
        return \gcc_jit_context_new_case(
            $this->jitContext->context,
            $value, 
            $value,
            $block
        );
    }

    private function implementString(\gcc_jit_function_ptr $func, \gcc_jit_param_ptr $obj, \gcc_jit_param_ptr $indent): \gcc_jit_case_ptr {
        $block = \gcc_jit_function_new_block($func, 'string_');
        $str = $this->jitContext->type->value->readValue(JITVariable::TYPE_STRING, $obj->asRValue());
        $size = $this->jitContext->type->string->sizePtr($str)->asRValue();
        $this->jitContext->helper->eval(
            $block,
            $this->jitContext->helper->call(
                'printf',
                $this->jitContext->constantFromString("%*sstring(%d) \"%.*s\"\n"),
                $indent->asRValue(),
                $this->jitContext->constantFromString(""),
                $size,
                $size,
                $this->jitContext->type->string->valuePtr($str)
            )
        );
        \gcc_jit_block_end_with_void_return($block, $this->jitContext->location());
        $value = \gcc_jit_context_new_rvalue_from_long(
            $this->jitContext->context,
            $this->jitContext->getTypeFromString('unsigned char'),
            JITVariable::TYPE_STRING
        );
        return \gcc_jit_context_new_case(
            $this->jitContext->context,
            $value, 
            $value,
            $block
        );
    } 

}