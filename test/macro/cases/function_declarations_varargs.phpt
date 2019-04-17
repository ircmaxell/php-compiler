--TEST--
Test function declarations with varargs
--FILE--
<?php

declare {
    function foo(int64, ...): int32;
    inline function bar(...): void;
    static function baz(void*, ...): void;
}


?>
--EXPECTF--
<?php

$fntype___0 = $this->context->context->functionType(
    $this->context->getTypeFromString('int32'),
    true , 
    $this->context->getTypeFromString('int64')            
);
$fn___0 = $this->context->module->addFunction('foo', $fntype___0);
$this->context->registerFunction('foo', $fn___0);

$fntype___0 = $this->context->context->functionType(
    $this->context->getTypeFromString('void'),
    true
);
$fn___0 = $this->context->module->addFunction('bar', $fntype___0);
$fn___0->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
$this->context->registerFunction('bar', $fn___0);

$fntype___0 = $this->context->context->functionType(
    $this->context->getTypeFromString('void'),
    true ,
    $this->context->getTypeFromString('void*')
);
$fn___0 = $this->context->module->addFunction('baz', $fntype___0);
$this->context->registerFunction('baz', $fn___0);
?>
