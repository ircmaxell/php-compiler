<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\standard;

function var_dump(...$vars): void {
    for ($i = 0; $i < count($vars); $i++) {
        var_dump_internal($vars[$i], 1);
    }
}

function var_dump_internal($var, int $level): void {
    if ($level > 1) {
        echo str_repeat(' ', $level - 1);
    }
    if (is_int($var)) {
        echo 'int(', $var, ")\n";
    } elseif (is_float($var)) {
        echo 'float(', $var, ")\n";
    } elseif (is_string($var)) {
        echo 'string(', \strlen($var), ') "', $var, "\")\n";
    } elseif (is_bool($var)) {
        echo 'bool(', $var ? 'true' : 'false', ")\n";
    } elseif (is_null($var)) {
        echo "NULL\n";
    } else {
        echo "unknown()\n";
    }
}
