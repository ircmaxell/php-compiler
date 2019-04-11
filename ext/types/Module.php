<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\types;

use PHPCompiler\ModuleAbstract;
use PHPCompiler\VM\Variable;

class Module extends ModuleAbstract
{
    public function getFunctions(): array
    {
        return [
            new strlen(),
            new is_type('is_int', Variable::TYPE_INTEGER),
            new is_type('is_integer', Variable::TYPE_INTEGER),
            new is_type('is_long', Variable::TYPE_INTEGER),
            new is_type('is_float', Variable::TYPE_FLOAT),
            new is_type('is_double', Variable::TYPE_FLOAT),
            new is_type('is_string', Variable::TYPE_STRING),
            new is_type('is_bool', Variable::TYPE_BOOLEAN),
            new is_type('is_null', Variable::TYPE_NULL),
        ];
    }
}
