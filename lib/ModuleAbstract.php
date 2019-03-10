<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

abstract class ModuleAbstract implements Module {

    public function getName(): string {
        return str_replace('\\', '_', get_class($this));
    }

    public function getFunctions(): array {
        return [];
    }

    public function init(Runtime $runtime): void {

    }

    public function shutdown(): void {
        
    }

}