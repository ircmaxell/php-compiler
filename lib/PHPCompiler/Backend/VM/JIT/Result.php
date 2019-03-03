<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT;

use PHPCompiler\Backend\VM\Handler;

class Result {
    private \gcc_jit_result_ptr $result;
    private int $loadType;

    public function __construct(\gcc_jit_result_ptr $result, int $loadType) {
        $this->result = $result;
        $this->loadType = $loadType;
        if ($loadType !== Builtin::LOAD_TYPE_IMPORT) {
            // Call the initialization function!
            $cb = $this->getCallable('__init__', 'void(*)()');
            $cb();
        }
    }

    public function __destruct() {
        if ($this->loadType !== Builtin::LOAD_TYPE_IMPORT) {
            // Call the initialization function!
            $cb = $this->getCallable('__shutdown__', 'void(*)()');
            $cb();
        }
        \gcc_jit_result_release($this->result);
    }

    public function getHandler(string $funcName, string $callbackType): Handler {
        return new Handler\JIT($this->getCallable($funcName, $callbackType), $this);
    }

    public function getCallable(string $funcName, string $callbackType): callable {
        $void = \gcc_jit_result_get_code($this->result, $funcName);
        return \__gcc_jit_getCallable(
            $callbackType, 
            $void
        );

    }
}