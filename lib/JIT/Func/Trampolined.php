<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Func;
use PHPCompiler\JIT\FuncAbstract;
use PHPCompiler\JIT\Context;

class Trampolined extends FuncAbstract {

    private array $trampolines = [];

    public function __construct(
        Context $context,
        string $name, 
        \gcc_jit_function_ptr $func, 
        \gcc_jit_type_ptr $returnType, 
        \gcc_jit_param_ptr ... $params
    ) {
        parent::__construct($context, $name, $func, $returnType, ...$params);
        $this->trampolines[$this->buildNativeSignature()] = $func;
    }

    public function call(\gcc_jit_rvalue_ptr ...$args): \gcc_jit_rvalue_ptr {
        $signature = $this->buildSignature(...$args);
        if (!isset($this->trampolines[$signature])) {
            $this->buildTrampoline($signature, ...$args);
        }
        return \gcc_jit_context_new_call(
            $this->context->context,
            $this->context->location(),
            $this->trampolines[$signature],
            count($args),
            \gcc_jit_rvalue_ptr_ptr::fromArray(...$args)
        );
    }

    private function buildNativeSignature(): string {
        $params = [];
        foreach ($this->params as $param) {
            $params[] = \gcc_jit_param_as_rvalue($param);
        }
        return $this->buildSignature(...$params);
    }

    private function buildTrampoline(string $signature, \gcc_jit_rvalue_ptr ... $args): void {
        var_dump($signature, $this->buildNativeSignature());
        die("Could not build trampoline function\n");
    }
}