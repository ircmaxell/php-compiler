<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Handler;

use PHPCompiler\JIT\Context as JITContext;
use PHPCompiler\VM\Context as VMContext;
use PHPCompiler\Handler;

abstract class Builtins implements Handler {

    abstract public function registerVM(VMContext $context): void;
    abstract public function registerJIT(JITContext $context): void;

    public static function loadVM(VMContext $context): void {
        foreach (self::loadBuiltins() as $handler) {
            $handler->registerVM($context);
        }
    }

    public static function loadJIT(JITContext $context): void {
        foreach (self::loadBuiltins() as $handler) {
            $handler->registerJIT($context);
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