--TEST--
Test compilation of binary ops
--FILE--
<?php

unsigned compile {
    $result = $left + $right;
    $result = $left - $right;
    $result = $left * $right;
    $result = $left / $right;
    $result = $left % $right;
    $result = $left & $right;
    $result = $left | $right;
    $result = $left < $right;
    $result = $left <= $right;
    $result = $left > $right;
    $result = $left >= $right;
    $result = $left == $right;
    $result = $left != $right;
}


compile {
    $result = $left + 0;
    $result = $left - 1;
    $result = $left * 2;
    $result = $left / 3;
    $result = $left % 4;
    $result = $left & 5;
    $result = $left | 6;
    $result = $left < 7;
    $result = $left <= 8;
    $result = $left > 9;
    $result = $left >= 10;
    $result = $left == 11;
    $result = $left != 12;
}

?>
--EXPECTF--
<?php

$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->addNoUnsignedWrap($left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->subNoUnsignedWrap($left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->mulNoUnsignedWrap($left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->unsignedDiv($left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->unsignedRem($left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->bitwiseAnd($left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->bitwiseOr($left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$cmp = \PHPLLVM\Builder::INT_ULT;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$cmp = \PHPLLVM\Builder::INT_ULE;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$cmp = \PHPLLVM\Builder::INT_UGT;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$cmp = \PHPLLVM\Builder::INT_UGE;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->icmp(\PHPLLVM\Builder::INT_EQ, $left, $__right);
$__right = $this->context->builder->intCast($right, $left->typeOf());
$result = $this->context->builder->icmp(\PHPLLVM\Builder::INT_NE, $left, $__right);


$__right = $left->typeOf()->constInt(0, false);
$result = $this->context->builder->addNoSignedWrap($left, $__right);
$__right = $left->typeOf()->constInt(1, false);
$result = $this->context->builder->subNoSignedWrap($left, $__right);
$__right = $left->typeOf()->constInt(2, false);
$result = $this->context->builder->mulNoSignedWrap($left, $__right);
$__right = $left->typeOf()->constInt(3, false);
$result = $this->context->builder->signedDiv($left, $__right);
$__right = $left->typeOf()->constInt(4, false);
$result = $this->context->builder->signedRem($left, $__right);
$__right = $left->typeOf()->constInt(5, false);
$result = $this->context->builder->bitwiseAnd($left, $__right);
$__right = $left->typeOf()->constInt(6, false);
$result = $this->context->builder->bitwiseOr($left, $__right);
$__right = $left->typeOf()->constInt(7, false);
$cmp = \PHPLLVM\Builder::INT_SLT;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $left->typeOf()->constInt(8, false);
$cmp = \PHPLLVM\Builder::INT_SLE;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $left->typeOf()->constInt(9, false);
$cmp = \PHPLLVM\Builder::INT_SGT;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $left->typeOf()->constInt(10, false);
$cmp = \PHPLLVM\Builder::INT_SGE;
$result = $this->context->builder->icmp($cmp, $left, $__right);
$__right = $left->typeOf()->constInt(11, false);
$result = $this->context->builder->icmp(\PHPLLVM\Builder::INT_EQ, $left, $__right);
$__right = $left->typeOf()->constInt(12, false);
$result = $this->context->builder->icmp(\PHPLLVM\Builder::INT_NE, $left, $__right);
?>
