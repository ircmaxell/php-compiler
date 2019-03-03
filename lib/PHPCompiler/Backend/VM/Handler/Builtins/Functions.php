<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\Handler\Builtins;

use PHPCompiler\Backend\VM\Handler\Builtins;
use PHPCompiler\Backend\VM\Context;
use PHPCompiler\Backend\VM\Block;
use PHPCompiler\Backend\VM\Frame;
use PHPCompiler\Backend\VM\PHPVar;
use PHPTypes\Type;

abstract class Functions extends Builtins {

    public function register(Context $context): void {
        $block = new Block(null);
        $block->handler = $this;
        $context->functions[$this->getName()] = $block;
    }

    abstract public function getName(): string;

}