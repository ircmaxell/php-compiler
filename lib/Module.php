<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

interface Module {

    public function getName(): string;

    public function getFunctions(): array;

    public function init(Runtime $runtime): void;

    public function shutdown(): void;

}