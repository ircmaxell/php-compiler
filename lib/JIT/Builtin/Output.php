<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

class Output extends Builtin {

    public function register(): void {
        $this->context->helper->importFunction(
            'printf',
            'int',
            true,
            'const char*'
        );
        $this->context->helper->importFunction(
            'sprintf',
            'int',
            true,
            'char*',
            'const char*'
        );
        $this->context->helper->importFunction(
            'snprintf',
            'int',
            true,
            'char*',
            'size_t',
            'const char*'
        );
    }

}