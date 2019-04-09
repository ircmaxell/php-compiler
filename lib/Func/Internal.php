<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Func;

use PHPCompiler\Handler;
use PHPCompiler\Func;
use PHPCompiler\Frame;
use PHPCompiler\VM\Context;
use PHPCompiler\VM\Variable;
use PHPCompiler\JIT;

abstract class Internal extends Func implements Handler {

    public function __construct(string $name = null) {
        if (is_null($name)) {
            $parts = explode('\\', get_class($this));
            $name = end($parts);
        }
        parent::__construct($name);
    }

    public function getFrame(Context $context, ?Frame $frame = null): Frame {
        return new Frame($this, null, null);
    }

    public function jit(JIT $jit): void;

}