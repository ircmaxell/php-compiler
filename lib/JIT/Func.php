<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

class Func {

    public string $name;
    public \gcc_jit_function_ptr $func;
    public \gcc_jit_type_ptr $returnType;
    public array $params;
    public $defineCallback = null;

    public function __construct(
        string $name, 
        \gcc_jit_function_ptr $func, 
        \gcc_jit_type_ptr $returnType, 
        \gcc_jit_param_ptr ... $params
    ) {
        $this->name = $name;
        $this->func = $func;
        $this->returnType = $returnType;
        $this->params = $params;
    }
}