<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Handler\Builtins;

use PHPCompiler\Handler\Builtins;
use PHPCompiler\JIT\Context as JITContext;
use PHPCompiler\JIT\Builtin as JITBuiltin;
use PHPCompiler\VM\Context as VMContext;
use PHPCompiler\Block;

abstract class Functions extends Builtins {

    protected ?JITContext $jitContext = null;

    public function registerVM(VMContext $context): void {
        $block = new Block(null);
        $block->handler = $this;
        $context->functions[$this->getName()] = $block;
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
        $func = $this->jitContext->helper->createFunction(
            $kind,
            $funcName,
            $this->getReturnType(),
            false,
            ...$this->getParamTypes()
        );
        $this->jitContext->registerFunction(
            $funcName,
            $func
        );
        if ($kind !== \GCC_JIT_FUNCTION_IMPORTED) {
            $this->implement(
                $func->func,
                ...$func->params
            );
        }
        $context->functions[$lcname] = $func->func;
    }

    abstract public function getName(): string;
    abstract public function getReturnType(): string;
    abstract public function getParamTypes(): array;
    abstract public function implement(\gcc_jit_function_ptr $func, \gcc_jit_param_ptr ...$params): void;

}