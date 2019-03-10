<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCompiler\VM\Variable;

class Frame {
    public ?Block $block;
    public ?Frame $parent;
    public int $pos = 0;
    /**
     * @var Variable[] $scope
     */
    public array $scope;

    public ?Func $call = null;
    public array $callArgs = [];
    public array $calledArgs = [];
    public ?Variable $returnVar = null;
    public ?Handler $handler = null;

    public function __construct(?Handler $handler, ?Block $block, ?Frame $parent, Variable ...$scope) {
        $this->handler = $handler;
        $this->block = $block;
        if (is_null($handler) && is_null($block)) {
            throw new \LogicException("Both handler and block cannot be null, one must be non-null");
        }
        $this->parent = $parent;
        $this->scope = $scope;
    }

    public function hasHandler(): bool {
        return !is_null($this->handler);
    }
}