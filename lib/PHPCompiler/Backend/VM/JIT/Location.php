<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT;

use PHPCompiler\Backend\VM\Handler;


class Location {
    public string $filename;
    public int $line;
    public int $column;
    public ?Location $prev = null;

    public function __construct(string $filename, int $line, int $column, ?Location $prev = null) {
        $this->filename = $filename;
        $this->line = $line;
        $this->column = $column + 1; // convert to 1 based indexes
        $this->prev = $prev;
    }

}