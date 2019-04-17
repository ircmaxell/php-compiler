<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

require __DIR__.'/../vendor/autoload.php';

$onlyChanged = false;

if (isset($argv[1]) && $argv[1] === 'onlyChanged') {
    $onlyChanged = true;
}

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__.'/../', FilesystemIterator::CURRENT_AS_PATHNAME),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($it as $dir) {
    if (! is_dir($dir)) {
        continue;
    }
    if (substr($dir, -1) === '.') {
        continue;
    }
    if (substr($dir, -6) === 'vendor') {
        // ensure we don't delve into the vendor directory
        continue;
    }
    foreach (new GlobIterator($dir.'/*.pre', FilesystemIterator::CURRENT_AS_PATHNAME) as $file) {
        $file = realpath($file);
        echo "Compiling ${file}\n";
        $destination = preg_replace('(\.pre$)', '.php', $file);
        if ($onlyChanged && filemtime($destination) >= filemtime($file)) {
            continue;
        }
        Pre\Plugin\compile($file, $destination);
    }
}

echo "Updating demo files\n";

exec(escapeshellcmd(\PHP_BINARY).' '.escapeshellarg(__DIR__.'/../bin/jit.php').' -y '.escapeshellarg(__DIR__.'/demo.php'));
