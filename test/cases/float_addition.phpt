--TEST--
Float Addition
--FILE--
<?php

echo 2.0 + 2.0;
echo "\n";

$a = 10;
$b = 1.5;
$c = 2.5;
echo $a * $c;
echo "\n";
--EXPECT--
4
25