<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\VM;

class Refcount {
    public int $refcount = 0;

    public function assertSeparated(): void {
        if ($this->refcount > 1) {
            throw new \LogicException('Refcount is > 1, but was asserted to be 1');
        }
    }

}