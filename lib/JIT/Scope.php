<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

use PHPCfg\Operand;

use PHPLLVM;

class Scope {
    public int $classId = 0;
    public \SplObjectStorage $blockStorage;
    public \SplObjectStorage $variables;
    public ?PHPLLVM\Value $toCall = null;
    public array $args = [];

    public function __construct() {
        $this->blockStorage = new \SplObjectStorage;
        $this->variables = new \SplObjectStorage;
    }
}