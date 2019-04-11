<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

function fibo_r(int $n): int
{
    return ($n < 2) ? 1 : fibo_r($n - 2) + fibo_r($n - 1);
}
function fibo(int $n): void
{
    $r = fibo_r($n);
    echo $r;
    echo "\n";
}

fibo(30);
