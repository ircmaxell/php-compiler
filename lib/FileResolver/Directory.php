<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\FileResolver;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use PHPCompiler\FileResolver;

class Composer implements FileResolver {

    protected array $extensions = ['php'];

    public function resolve(string $dir): array {