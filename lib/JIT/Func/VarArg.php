<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Func;
use PHPCompiler\JIT\Func;
use PHPCompiler\JIT\Context;

class VarArg extends Func {

    private static int $proxyCounter = 0;

    private string $paramType;

    private array $trampolines = [];
    public \gcc_jit_param_ptr $nargs;
    public \gcc_jit_param_ptr $varargs;

    public function __construct(
        Context $context,
        string $name, 
        \gcc_jit_function_ptr $func, 
        \gcc_jit_type_ptr $returnType, 
        string $paramType,
        \gcc_jit_param_ptr $nargs,
        \gcc_jit_param_ptr $varargs,
        \gcc_jit_param_ptr ...$params
    ) {
        $this->paramType = $paramType;
        $this->nargs = $nargs;
        $this->varargs = $varargs;
        parent::__construct($context, $name, $func, $returnType, ...$params);
    }

    public function call(\gcc_jit_rvalue_ptr ...$args): \gcc_jit_rvalue_ptr {
        $signature = $this->buildSignature(...$args);
        if (!isset($this->trampolines[$signature])) {
            $this->buildTrampoline($signature, ...$args);
        }
        $array = $this->context->getTypeFromString($this->paramType . '[' . count($args) . ']');
        return \gcc_jit_context_new_call(
            $this->context->context,
            $this->context->location(),
            $this->trampolines[$signature],
            count($args),
            \gcc_jit_rvalue_ptr_ptr::fromArray(...$args)
        );
    }

    private function buildTrampoline(string $signature, \gcc_jit_rvalue_ptr ...$params): void {
        $trampolineParams = [];
        foreach ($params as $key => $param) {
            $trampolineParams[] = \gcc_jit_context_new_param(
                $this->context->context, 
                $this->context->location(), 
                \gcc_jit_rvalue_get_type($param), 
                'arg_' . $key
            );
        }
        $func = \gcc_jit_context_new_function(
            $this->context->context,
            $this->context->location(),
            \GCC_JIT_FUNCTION_ALWAYS_INLINE,
            $this->returnType,
            $this->name . '__proxy__' . (self::$proxyCounter++),
            count($trampolineParams),
            \gcc_jit_param_ptr_ptr::fromArray(...$trampolineParams),
            0
        );
        $block = \gcc_jit_function_new_block($func, 'main');
        $this->buildTrampolineBody($func, $block, ...$trampolineParams);
        $this->trampolines[$signature] = $func;
    }

    private function buildTrampolineBody(\gcc_jit_function_ptr $func, \gcc_jit_block_ptr $block, \gcc_jit_param_ptr ...$params): void {
        $callArgs = [];
        $varArgs = [];
        $varArgType = $this->context->getTypeFromString($this->paramType);
        foreach ($params as $key => $param) {
            if (isset($this->params[$key])) {
                // native arg
                $callArgs[] = $this->convertArgForParam($func, $block, \gcc_jit_param_get_type($this->params[$key]), $param->asRValue());
            } else {
                $varArgs[] = $param->asRValue();
            }
        }
        if (count($callArgs) !== count($this->params)) {
            throw new \LogicException('Required non-variadic param not supplied');
        }
        $callArgs[] = $this->context->constantFromInteger(count($varArgs), 'size_t');
        $local = gcc_jit_function_new_local(
            $func, 
            $this->context->location(), 
            $this->context->getTypeFromString($this->paramType . '[' . count($varArgs) . ']'),
            'varargs'
        );
        foreach ($varArgs as $key => $arg) {

            \gcc_jit_block_add_assignment(
                $block,
                $this->context->location(),
                \gcc_jit_context_new_array_access(
                    $this->context->context,
                    $this->context->location(),
                    $local->asRValue(),
                    $this->context->constantFromInteger($key, 'size_t')
                ),
                $this->convertArgForParam($func, $block, $varArgType, $arg)
            );
        }
        $callArgs[] = $this->context->helper->castArrayToPointer($local->asRValue(), $this->paramType);
        $call = \gcc_jit_context_new_call(
            $this->context->context,
            $this->context->location(),
            $this->func,
            count($callArgs),
            \gcc_jit_rvalue_ptr_ptr::fromArray(...$callArgs)
        );
        if ($this->context->getStringFromType($this->returnType) === 'void') {
            $this->context->helper->eval($block, $call);
            \gcc_jit_block_end_with_void_return($block, $this->context->location());
        } else {
            \gcc_jit_block_end_with_return($block, $this->context->location(), $call);
        }
    }

}