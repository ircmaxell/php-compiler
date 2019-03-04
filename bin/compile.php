<?php

use PHPCompiler\Runtime;

function run(string $filename, string $code, array $options) {
    $runtime = new Runtime;
    $block = $runtime->parseAndCompile($code, $filename);
    if (!isset($options['-l'])) {
        if (!isset($options['-o']) || $options['-o'] === true) {
            $options['-o'] = str_replace('.php', '', $filename);
        }
        $runtime->standalone($block, $options['-o']);
    }
}

require_once __DIR__ . '/../src/cli.php';