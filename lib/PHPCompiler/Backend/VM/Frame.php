<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

class Frame {
    public Block $block;
    public ?Frame $parent;
    public int $pos = 0;
    /**
     * @var PHPVar[] $scope
     */
    public array $scope;

    public ?Block $call = null;
    public array $callArgs = [];
    public array $calledArgs = [];
    public ?PHPVar $returnVar = null;

    public function __construct(Block $block, ?Frame $parent, PHPVar ...$scope) {
        $this->block = $block;
        $this->parent = $parent;
        $this->scope = $scope;
    }
}