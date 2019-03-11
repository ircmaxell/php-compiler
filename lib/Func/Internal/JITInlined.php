<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Func\Internal;
use PHPCompiler\Func\Internal;
use PHPCompiler\JIT;
use PHPCompiler\JIT\Func as JITFunc;

abstract class JITInlined extends Internal implements JITFunc {
    protected JIT $jit;

    public function jit(JIT $jit): JITFunc {
        $this->jit = $jit;
        return $this;
    }

}