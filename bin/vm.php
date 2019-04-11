<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

use PHPCompiler\Runtime;

function run(string $filename, string $code, array $options): void
{
    $runtime = new Runtime();
    $block = $runtime->parseAndCompile($code, $filename);
    if (! isset($options['-l'])) {
        $runtime->run($block);
    }
}

require_once __DIR__.'/../src/cli.php';
