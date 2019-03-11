<?php

use PHPCompiler\Runtime;

function run(string $filename, string $code, array $options) {
    $runtime = new Runtime;

    $debugFile = null;
    if (isset($options['-y'])) {
        if ($options['-y'] === true) {
            $debugFile = str_replace('.php', '', $filename);
        } else {
            $debugFile = $options['-y'];
        }
        if (substr($debugFile, 0, 1) !== '/') {
            $debugFile = getcwd() . '/' . $debugFile;
        }
        $runtime->setDebug($debugFile);
    }
    $block = $runtime->parseAndCompile($code, $filename);
    $runtime->jit($block);
    
    if (!isset($options['-l'])) {
        $runtime->run($block);
    }
}

require_once __DIR__ . '/../src/cli.php';