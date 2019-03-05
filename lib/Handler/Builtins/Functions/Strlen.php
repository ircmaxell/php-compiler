<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Handler\Builtins\Functions;

use PHPCompiler\Handler\Builtins\Functions;
use PHPCompiler\Frame;
use PHPCompiler\VM\Variable;
use PHPTypes\Type;

class Strlen extends Functions {

    public function getName(): string {
        return 'strlen';
    }

    public function execute(Frame $frame): void {
        $var = $frame->calledArgs[0];
        if (!is_null($frame->returnVar)) {
            $frame->returnVar->type = Variable::TYPE_INTEGER;
            $frame->returnVar->integer = strlen($var->toString());
        }
    }

}