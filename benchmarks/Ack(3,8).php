<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

function Ack(int $m, int $n): int
{
    if ($m == 0) {
        return $n + 1;
    }
    if ($n == 0) {
        return Ack($m - 1, 1);
    }

    return Ack($m - 1, Ack($m, ($n - 1)));
}

echo Ack(3, 8);
echo "\n";
