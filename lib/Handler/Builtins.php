<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Handler;

use PHPCompiler\VM\Context;
use PHPCompiler\Handler;

abstract class Builtins implements Handler {

    abstract public function register(Context $context): void;

    public static function load(Context $context): void {
        foreach (self::loadBuiltins() as $handler) {
            $handler->register($context);
        }
    }

    private static function loadBuiltins(): \Generator {
        $basePath = realpath(__DIR__ . '/Builtins');
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath)
        );
        foreach ($it as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $fileBase = realpath($file->getPathname());
            if (dirname($fileBase) === $basePath) {
                // skip the first folder
                continue;
            }
            $suffix = str_replace(dirname($basePath), '', $fileBase);
            $suffix = str_replace('.php', '', $suffix);
            $suffix = str_replace(DIRECTORY_SEPARATOR, '\\', $suffix);
            $class = __NAMESPACE__ . $suffix;
            yield new $class;
        }
    }

}