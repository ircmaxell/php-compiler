<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\standard;

use PHPCompiler\Func\Internal;
use PHPCompiler\Frame;
use PHPCompiler\VM\Variable as VMVariable;
use PHPCompiler\VM\ClassEntry;
use PHPTypes\Type;

class var_dump extends Internal {

    public function execute(Frame $frame): void {
        foreach ($frame->calledArgs as $arg) {
            $this->var_dump($arg, 1);
        }
    }

    private function var_dump(VMVariable $var, int $level) {
        if ($level > 1) {
            echo str_repeat(' ', $level - 1);
        }
restart:
        switch ($var->type) {
            case VMVariable::TYPE_INTEGER:
                echo 'int(', $var->toInt(), ")\n";
                break;
            case VMVariable::TYPE_FLOAT:
                echo 'float(', $var->toFloat(), ")\n";
                break;
            case VMVariable::TYPE_STRING:
                echo 'string(', strlen($var->toString()), ') "', $var->toString(), "\")\n";
                break;
            case VMVariable::TYPE_BOOLEAN:
                echo 'bool(', $var->toBool() ? 'true' : 'false', ")\n";
                break;
            case VMVariable::TYPE_INDIRECT:
                $var = $var->resolveIndirect();
                goto restart;
            default:
                throw new \LogicException("var_dump not implemented for type");
        }
    }

}