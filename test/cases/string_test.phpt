--TEST--
Basic String Operations
--FILE--
<?php
$a = "hello";
$b = "world";
$c = "$a $b";
echo "$a\n";
echo "$b\n";
echo "$c\n";
--EXPECT--
hello
world
hello world
