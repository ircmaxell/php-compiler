--TEST--
Function declaration
--FILE--
<?php

function id(int $a): int {
    return $a;
}

echo id(123);
--EXPECT--
123