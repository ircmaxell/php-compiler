<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\Type;

use PHPCfg\Operand;
use PHPCfg\Operand\Literal;
use PHPCompiler\JIT\Builtin\Type;
use PHPCompiler\JIT\Builtin\Refcount;
use PHPCompiler\JIT\Variable;


class Class_ extends Type {

    private array $classes = [];

    public function register(): void {}

    public function declare(Operand $name): int {
        if (!$name instanceof Literal) {
            throw new \LogicException('JIT only supports constant named classes');
        }
        return $this->classes[strtolower($name->value)] = count($this->classes);
    }

    public function getSize(int $classId): \gcc_jit_rvalue_ptr {
        return $this->context->constantFromInteger(0, 'size_t');
    }

    public function lookup(Operand $name) {
        if (!$name instanceof Literal) {
            
            throw new \LogicException('JIT only supports constant named classes');
        }
        $lcname = strtolower($name->value);
        if (!isset($this->classes[$lcname])) {
            throw new \LogicException("Unknown class lookup: $name");
        }
        return $this->classes[$lcname];
    }

}