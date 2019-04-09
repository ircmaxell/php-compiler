<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Call;

use PHPCompiler\JIT\Context;
use PHPCompiler\JIT\Call;
use PHPCompiler\JIT\Variable;

use PHPLLVM\Value;

class Native implements Call {

    public Value $function;
    public string $name;

    public function __construct(Value $function, string $name) {
        $this->function = $function;
        $this->name = $name;
    }

    public function call(Context $context, Variable ... $args): Value {
        $argValues = array_map(function($arg) use ($context) {
            return $context->helper->loadValue($arg);
        }, $args);
        return $context->builder->call(
            $this->function,
            ...$argValues
        );
    }

}