<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT;

use PHPCompiler\Backend\VM\Handler;

use PHPCfg\Operand;

class Scope {

    public \SplObjectStorage $blockStorage;
    public \SplObjectStorage $variables;
    public ?\gcc_jit_function_ptr $toCall = null;
    public array $args = [];

    public function __construct() {
        $this->blockStorage = new \SplObjectStorage;
        $this->variables = new \SplObjectStorage;
    }
}