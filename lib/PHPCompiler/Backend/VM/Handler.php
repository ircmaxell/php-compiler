<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;


class Handler {

    public $callback;
    private \gcc_jit_result_ptr $result;

    public function __construct(callable $cb, \gcc_jit_result_ptr $result) {
        $this->callback = $cb;
        $this->result = $result;
    }

    public function __destruct() {
        \gcc_jit_result_release($this->result);
    }

}