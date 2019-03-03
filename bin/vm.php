<?php

use PHPCompiler\Backend\VM\Runtime;

require __DIR__ . '/../vendor/autoload.php';

$opts = $argv;
// get rid of this
array_shift($opts);

$execFile = '';
$execCode = '';
while (!empty($opts)) {
    $opt = array_shift($opts);
    switch ($opt) {
        case '-r':
            $execCode = '<?php ' . array_shift($opts);
            $execFile = 'Command line code';
            break;
        default:
            if (!empty($opts)) {
                die("Extra argument not understood: $opt\n");
            }
            if (!empty($execCode)) {
                die("Unsupported argument combination leading to multiple executions\n");
            }
            if (substr($opt, 0, 1) === '-') {
                if (strlen($opt) === 1) {
                    $execFile = '-';
                    $execCode = stream_get_contents(STDIN);
                    break;
                } else {
                    die("Unsupported bare argument $opt\n");
                }
            }
            if (!file_exists($opt)) {
                die("Could not open file $opt\n");
            }
            $execCode = file_get_contents($opt);
            $execFile = $opt;
    }
}

if (empty($execCode)) {
    $execFile = '-';
    $execCode = stream_get_contents(STDIN);
}

run($execFile, $execCode);

function run(string $filename, string $code) {
    $runtime = new Runtime;
    $block = $runtime->parseAndCompile($code, $filename);
    $runtime->run($block);
    die();
}