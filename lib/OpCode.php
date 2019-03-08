<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

class OpCode {
    const TYPE_ECHO = 1;
    const TYPE_ASSIGN = 2;
    const TYPE_CONCAT = 3;
    const TYPE_JUMP = 4;
    const TYPE_CONST_FETCH = 5;
    const TYPE_JUMPIF = 6;
    const TYPE_PLUS = 7;
    const TYPE_SMALLER = 8;
    const TYPE_RETURN_VOID = 9;
    const TYPE_FUNCDEF = 10;
    const TYPE_FUNCCALL_INIT = 11;
    const TYPE_ARG_SEND = 12;
    const TYPE_ARG_RECV = 13;
    const TYPE_FUNCCALL_EXEC_RETURN = 14;
    const TYPE_FUNCCALL_EXEC_NORETURN = 15;
    const TYPE_IDENTICAL = 16;
    const TYPE_RETURN = 17;
    const TYPE_MINUS = 18;
    const TYPE_DECLARE_CLASS = 19;
    const TYPE_NEW = 20;
    const TYPE_MUL = 21;
    const TYPE_DIV = 22;
    const TYPE_GREATER = 23;
    const TYPE_DECLARE_PROPERTY = 24;
    const TYPE_PROPERTY_FETCH = 25;
    const TYPE_UNARY_MINUS = 26;
    const TYPE_UNARY_PLUS = 27;
    const TYPE_BITWISE_NOT = 28;
    const TYPE_BOOLEAN_NOT = 29;
    const TYPE_PRINT = 30;
    const TYPE_CLONE = 31;
    const TYPE_EMPTY = 32;
    const TYPE_EVAL = 33;
    const TYPE_EXIT = 34;
    const TYPE_SMALLER_OR_EQUAL = 35;
    const TYPE_GREATER_OR_EQUAL = 36;
    const TYPE_CAST_ARRAY = 37;
    const TYPE_CAST_BOOL = 38;
    const TYPE_CAST_FLOAT = 39;
    const TYPE_CAST_INT = 40;
    const TYPE_CAST_OBJECT = 41;
    const TYPE_CAST_STRING = 42;
    const TYPE_CAST_UNSET = 43;
    const TYPE_EQUAL = 44;
    const TYPE_ARRAY_DIM_FETCH=45;
    const TYPE_MODULO = 46;
    const TYPE_SWITCH = 47;
    const TYPE_CASE = 48;
    const TYPE_BITWISE_AND = 49;
    const TYPE_BITWISE_OR = 50;
    const TYPE_BITWISE_XOR = 51;

    public int $type;
    public ?int $arg1;
    public ?int $arg2;
    public ?int $arg3;
    public ?Block $block1 = null;
    public ?Block $block2 = null;

    public function __construct(int $type, ?int $arg1 = null, ?int $arg2 = null, ?int $arg3 = null) {
        $this->type = $type;
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
    }

    public function getType(): string {
        $r = new \ReflectionClass(__CLASS__);
        foreach ($r->getConstants() as $name => $value) {
            if ($value === $this->type) {
                return $name;
            }
        }
        return 'unknown opcode';
    }
}