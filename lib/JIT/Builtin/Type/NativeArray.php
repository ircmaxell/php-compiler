<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\Type;

use PHPCompiler\JIT\Builtin\Type;
use PHPCompiler\JIT\Builtin\Refcount;
use PHPCompiler\JIT\Variable;

use PHPCompiler\JIT\Builtin\ErrorHandler;

class NativeArray extends Type {

    public function register(): void {
        $this->context->registerFunction(
            '__nativearray__boundscheck',
            $this->context->helper->createNativeFunction(
                \GCC_JIT_FUNCTION_ALWAYS_INLINE,
                '__nativearray__boundscheck',
                'void',
                false,
                'long long',
                'long long'
            )
        );
    }

    public function initialize(): void {
        $bounds = $this->context->lookupFunction('__nativearray__boundscheck');
        $block = \gcc_jit_function_new_block($bounds->func, 'main');
        $good = \gcc_jit_function_new_block($bounds->func, 'good');
        $bad = \gcc_jit_function_new_block($bounds->func, 'bad');
        \gcc_jit_block_end_with_conditional(
            $block,
            $this->context->location(),
            \gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                \GCC_JIT_COMPARISON_GE,
                $bounds->params[0]->asRValue(),
                $bounds->params[1]->asRValue()
            ),
            $bad,
            $good
        );
        \gcc_jit_block_end_with_void_return($good, $this->context->location());
        $this->context->error->error($bad, ErrorHandler::E_ERROR, "Invalid bounds access");
        \gcc_jit_block_end_with_void_return($bad, $this->context->location());
    }
}