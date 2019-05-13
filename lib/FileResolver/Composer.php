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
        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }
        while (is_dir($dir) && $dir !== '/') {
            if (file_exists($dir . '/vendor/composer/autoload_static.php')) {
                return $this->loadFrom($dir);
            }
            $dir = realpath($dir . '/..');
        }
        throw new \LogicException("Could not find a valid composer install in folder tree");
    }

    protected function loadFrom(string $dir): array {
        $result = [];
        $files = require $dir . '/vendor/composer/autoload_files.php';
        foreach ($files as $file) {
            $result[] = realpath($file);
        }
        $classmap = require $dir . '/vendor/composer/autoload_classmap.php';
        foreach ($classmap as $file) {
            $result[] = realpath($file);
        }
        $classPaths = array_merge(
            array_values(require $dir . '/vendor/composer/autoload_namespaces.php'),
            array_values(require $dir . '/vendor/composer/autoload_psr4.php')
        );
        $result = array_merge($result, $this->loadClassPaths($classPaths));
        $result = array_unique($result);
        return $result;
    }

    protected function loadClassPaths(array $paths): array {
        $result = [];
        foreach ($paths as $pathset) {
            foreach ($pathset as $path) {
                if (!is_dir($path)) {
                    continue;
                }
                $it = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(
                        $path
                    )
                );
                foreach ($it as $file) {
                    if (in_array($file->getExtension(), $this->extensions)) {
                        $result[] = realpath($file->getPathname());
                    }
                }
            }
        }
        return $result;
    }

}