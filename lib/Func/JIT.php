<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Func;

use PHPCompiler\Func;
use PHPCompiler\Frame;
use PHPCompiler\Handler;
use PHPCompiler\VM\Context;
use PHPCompiler\JIT\Result;


class JIT extends Func implements Handler {

    private $callback;
    private Result $result;

    public function __construct(string $name, callable $cb, Result $result) {
        parent::__construct($name);
        $this->callback = $cb;
        $this->result = $result;
    }

    public function getFrame(Context $context, ?Frame $frame = null): Frame {
        return new Frame($this, null, null);
    }

    public function execute(Frame $frame): void {
        // TODO: handle argument passing
        assert(empty($frame->calledArgs));
        ($this->callback)();
    }

}