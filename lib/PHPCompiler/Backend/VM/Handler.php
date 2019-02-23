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
    private JIT\Result $result;

    public function __construct(callable $cb, JIT\Result $result) {
        $this->callback = $cb;
        $this->result = $result;
    }

    

}