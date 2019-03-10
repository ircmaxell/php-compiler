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
use PHPCompiler\Block;
use PHPCompiler\VM\Context;
use PHPCompiler\VM\Variable;

class PHP extends Func {

    private Block $block;

    public function __construct(string $name, Block $block) {
        parent::__construct($name);
        $this->block = $block;
    }

    public function getFrame(Context $context, ?Frame $frame = null): Frame {
        return $this->block->getFrame($context, $frame);
    }

}