<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT;

use PHPCompiler\Backend\VM\JIT;

abstract class Builtin {
    const LOAD_TYPE_EXPORT = 1;
    const LOAD_TYPE_IMPORT = 2;
    const LOAD_TYPE_EMBED = 3;

    protected Context $context;
    protected int $loadType;

    public function __construct(Context $context, int $loadType) {
        $this->context = $context;
        $context->registerBuiltin($this);
        $this->loadType = $loadType;
        $this->register($loadType);
    }

    protected function register(int $loadType): void {
    }

    public function init(\gcc_jit_function_ptr $func, \gcc_jit_block_ptr $block): \gcc_jit_block_ptr {
        return $block;
    }

    protected function importFunction(
        string $funcName, 
        string $returnType, 
        bool $isVariadic, 
        string ...$params
    ): void {
        $this->context->register($funcName, $this->createFunction(
            \GCC_JIT_FUNCTION_IMPORTED,
            $funcName,
            $returnType,
            $isVariadic,
            ...$params
        ));
    }

    protected function createFunction(
        int $type, 
        string $funcName, 
        string $returnType, 
        bool $isVariadic, 
        string ...$params
    ): Func {
        $paramPointers = [];
        $i = 0;
        foreach ($params as $param) {
            $paramPointers[] = \gcc_jit_context_new_param (
                $this->context->context, 
                null, 
                $this->context->getTypeFromString($param), 
                "{$funcName}_{$i}"
            );
        }
        $gccReturnType = $this->context->getTypeFromString($returnType);
        return new Func(
            $funcName,
            \gcc_jit_context_new_function(
                $this->context->context, 
                null,
                $type,
                $gccReturnType,
                $funcName,
                count($paramPointers), 
                \gcc_jit_param_ptr_ptr::fromArray(
                    ...$paramPointers
                ),
                $isVariadic ? 1 : 0
            ),
            $gccReturnType,
            ...$paramPointers
        );
    }

}