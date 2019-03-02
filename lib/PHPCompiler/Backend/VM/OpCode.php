<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

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