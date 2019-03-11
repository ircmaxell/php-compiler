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

class Native extends FuncAbstract {

    public function call(\gcc_jit_rvalue_ptr ...$args): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_call(
            $this->context->context,
            $this->context->location(),
            $this->func,
            count($args),
            \gcc_jit_rvalue_ptr_ptr::fromArray(...$args)
        );
    }

}