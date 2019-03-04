<?php

use PHPCfg\Printer\Text as CfgPrinter;
use PHPCompiler\Printer as OpCodePrinter;
use PHPCompiler\Runtime;

function run(string $filename, string $code, array $options) {
    $runtime = new Runtime;
    $script = $runtime->parse($code, $filename);
    echo "\nControl Flow Graph: \n";
    echo (new CfgPrinter)->printScript($script);
    $block = $runtime->compile($script);
    echo "\n\nOpCodes:\n\n";
    echo (new OpCodePrinter())->print($block);
}

require_once __DIR__ . '/../src/cli.php';