<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

use PHPCfg\Printer\Text as CfgPrinter;
use PHPCompiler\Printer as OpCodePrinter;
use PHPCompiler\Runtime;

function run(string $filename, string $code, array $options): void
{
    $runtime = new Runtime();
    $script = $runtime->parse($code, $filename);
    echo "\nControl Flow Graph: \n";
    echo (new CfgPrinter())->printScript($script);
    $block = $runtime->compile($script);
    echo "\n\nOpCodes:\n\n";
    echo (new OpCodePrinter())->print($block);
}

require_once __DIR__.'/../src/cli.php';
