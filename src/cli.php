<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

require __DIR__.'/../vendor/autoload.php';

ini_set('memory_limit', '-1');
error_reporting(~0);

$opts = $argv;
// get rid of this
array_shift($opts);

$execFile = '';
$execCode = '';
$options = [];
while (! empty($opts)) {
    $opt = array_shift($opts);
    switch ($opt) {
        case '-l':
            $options['-l'] = true;

            break;
        case '-y':
            if (empty($opts) || substr($opts[0], 0, 1) === '-') {
                $options['-y'] = true;
            } elseif (count($opts) === 1 && substr($opts[0], -4) === '.php') {
                // will assume the same name as the input file...
                $options['-y'] = true;
            } else {
                $options['-y'] = array_shift($opts);
            }

            break;
        case '-o':
            if (empty($opts) || substr($opts[0], 0, 1) === '-') {
                $options['-o'] = true;
            } elseif (count($opts) === 1 && substr($opts[0], -4) === '.php') {
                // will assume the same name as the input file...
                $options['-o'] = true;
            } else {
                $options['-o'] = array_shift($opts);
            }

            break;
        case '-r':
            $execCode = '<?php '.array_shift($opts);
            $execFile = 'Command line code';

            break;
        default:
            if (! empty($opts)) {
                die("Extra argument not understood: ${opt}\n");
            }
            if (! empty($execCode)) {
                die("Unsupported argument combination leading to multiple executions\n");
            }
            if (substr($opt, 0, 1) === '-') {
                if (strlen($opt) === 1) {
                    $execFile = '-';
                    $execCode = stream_get_contents(\STDIN);

                    break;
                }
                die("Unsupported bare argument ${opt}\n");
            }
            if (! file_exists($opt)) {
                die("Could not open file ${opt}\n");
            }
            $execCode = file_get_contents($opt);
            $execFile = $opt;
    }
}

if (empty($execCode)) {
    $execFile = '-';
    $execCode = stream_get_contents(\STDIN);
}

if (function_exists('run')) {
    // @phan-suppress-next-line PhanUndeclaredFunction yes it is we just made a function_exists call
    run($execFile, $execCode, $options);
} else {
    throw new \RuntimeException('Must define run before including cli.php');
}
