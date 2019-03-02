--TEST--
Basic String Operations
--FILE--
<?php
$a = "hello";
$b = "world";
$c = "$a $b";
echo "$a\r\n";
echo "$b\r\n";
echo "$c\r\n";
--EXPECT--
hello
world
hello world
