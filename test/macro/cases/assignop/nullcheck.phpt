--TEST--
Test compilation of null checks
--FILE--
<?php

compile {
    $result = $var == null;
}


?>
--EXPECTF--
<?php

$result = $this->context->builder->icmp(\PHPLLVM\Builder::INT_EQ, $var, $var->typeOf()->constNull());

?>
