<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\VM;

class ClassProperty {

    public string $name;
    public ?Variable $default;
    public Variable $prototype;

    public function __construct(string $name, ?Variable $default, Variable $prototype) {
        $this->name = $name;
        $this->default = $default;
        $this->prototype = $prototype;
    }

    public function getVariable(): Variable {
        $var = clone $this->prototype;
        if (!is_null($this->default)) {
            $var->copyFrom($this->default);
        }

        return $var;
    }


}
