<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

function simple(): void
{
    $a = 0;
    for ($i = 0; $i < 1000000; ++$i) {
        $a++;
    }
    $thisisanotherlongname = 0;
    for ($thisisalongname = 0; $thisisalongname < 1000000; ++$thisisalongname) {
        $thisisanotherlongname++;
    }
}
/**/
function simplecall(): void
{
    for ($i = 0; $i < 1000000; ++$i) {
        strlen('hallo');
    }
}
/**/
function hallo(string $a): void
{
}
function simpleucall(): void
{
    for ($i = 0; $i < 1000000; ++$i) {
        hallo('hallo');
    }
}
/**/
function simpleudcall(): void
{
    for ($i = 0; $i < 1000000; ++$i) {
        hallo2('hallo');
    }
}
function hallo2(string $a): void
{
}

simple();
simplecall();
simpleucall();
simpleudcall();

echo "Done\n";
