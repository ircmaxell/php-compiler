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

class Directory implements FileResolver {

    protected array $extensions = ['php'];

    public function resolve(string $dir): array {
        $results = [];
        if (!is_dir($dir)) {
            return [];
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $dir
            )
        );
        foreach ($it as $file) {
            if (in_array($file->getExtension(), $this->extensions)) {
                $result[] = realpath($file->getPathname());
            }
        }
        return $results;
    }

}