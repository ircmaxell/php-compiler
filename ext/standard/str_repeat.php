<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\standard;

function str_repeat(string $input, int $multiplier): string {
    $result = '';
    for ($i = 0; $i < $multiplier; $i++) {
        $result .= $input;
    }
    return $result;
}