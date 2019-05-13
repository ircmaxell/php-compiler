<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

class ErrorHandler extends Builtin
{
    const E_NORMAL = 0;

    const E_ERROR = 1;

    const E_RECOVERABLE_ERROR = 2;

    public function register(): void
    {
    }

    public function initialize(): void
    {
    }
}
