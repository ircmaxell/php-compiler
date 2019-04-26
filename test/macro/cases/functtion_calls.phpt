--TEST--
Test function calls
--FILE--
<?php

compile {
    bar();
    foo($a);
    $a = bar();
    $b = foo($c);
}


?>
--EXPECTF--
<?php

$this->context->builder->call(
    $this->context->lookupFunction('bar')
);
$this->context->builder->call(
    $this->context->lookupFunction('foo') ,
    $a
);
$a = $this->context->builder->call(
    $this->context->lookupFunction('bar')
);
$b = $this->context->builder->call(
    $this->context->lookupFunction('foo') ,
    $c
);

?>