--TEST--
Class declaration
--FILE--
<?php

class A {
    public int $int;
    public string $string;
}

function hello(A $a): void {
    echo "Hello {$a->string}\n";
}

$a = new A;
$a->int = 3;
$a->string = "World";

$b = new A;
$b->int = 5;
$b->string = "Something";

echo "A:\n";
hello($a);
echo $a->int;
echo "\n";


echo "B:\n";
hello($b);
echo $b->int;
echo "\n";

echo "Okay";
--EXPECT--
A:
Hello World
3
B:
Hello Something
5
Okay