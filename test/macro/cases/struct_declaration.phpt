--TEST--
Test struct declarations
--FILE--
<?php

declare {
    struct foo {
        int64 $bar;
    }

    struct bar {
        foo $foo;
        foo* $foo_ptr;
    }
}


?>
--EXPECTF--
<?php

$struct___0 = $this->context->context->namedStructType('foo');
// declare first so recursive structs are possible :)
$this->context->registerType('foo', $struct___0);
$this->context->registerType('foo' . '*', $struct___0->pointerType(0));
$this->context->registerType('foo' . '**', $struct___0->pointerType(0)->pointerType(0));
$struct___0->setBody(
    false ,  // packed
    $this->context->getTypeFromString('int64')
);
$this->context->structFieldMap['foo'] = [
    'bar' => 0
];

$struct___0 = $this->context->context->namedStructType('bar');
// declare first so recursive structs are possible :)
$this->context->registerType('bar', $struct___0);
$this->context->registerType('bar' . '*', $struct___0->pointerType(0));
$this->context->registerType('bar' . '**', $struct___0->pointerType(0)->pointerType(0));
$struct___0->setBody(
    false ,  // packed
    $this->context->getTypeFromString('foo')
    , $this->context->getTypeFromString('foo*')
);
$this->context->structFieldMap['bar'] = [
    'foo' => 0
    , 'foo_ptr' => 1
];

?>