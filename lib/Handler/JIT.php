<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Handler;

use PHPCompiler\Frame;
use PHPCompiler\Handler;
use PHPCompiler\JIT\Result;


class JIT implements Handler {

    private $callback;
    private Result $result;

    public function __construct(callable $cb, Result $result) {
        $this->callback = $cb;
        $this->result = $result;
    }

    public function execute(Frame $frame): void {
        // TODO: handle argument passing
        assert(empty($frame->calledArgs));
        ($this->callback)();
    }

}