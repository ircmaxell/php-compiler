<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\standard;

use PHPCompiler\ModuleAbstract;

class Module extends ModuleAbstract
{
    public function getFunctions(): array
    {
        return [
            $this->parseAndCompileFunction('str_repeat', __DIR__.'/str_repeat.php'),
        ];
    }
}
