<?php

use PHPCompiler\Backend\VM\Runtime;

require __DIR__ . '/../vendor/autoload.php';

$opts = $argv;
// get rid of this
array_shift($opts);

if (count($opts) === 0) {
    run('stdin', stream_get_contents(STDIN));
}
if (count($opts) === 1 && $opts[0][0] !== '-') {
    $file = realpath($opts[0]);
    if (empty($file)) {
        die("Could not find file {$opts[0]}\n");
    }
    run($file, file_get_contents($file));
}

die('Unsupported argument set');

function run(string $filename, string $code) {
    $runtime = new Runtime;
    $block = $runtime->parseAndCompile($code, $filename);
    $runtime->jit($block);
    ($block->handler->callback)();
    die();
}