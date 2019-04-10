--TEST--
Basic bool operations
--FILE--
<?php
$a = true;

if ($a) {
	echo "True\n";
} else {
	echo "False\n";
}

$a = !$a
echo $a ? "True\n" : "False\n";
--EXPECT--
True
False
