<?php

# This file is generated, changes you make will be lost.
# Make your changes in /compiler/script/../ext/types/strlen.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\types;

use PHPCompiler\Func\Internal;
use PHPCompiler\Frame;

use PHPCompiler\JIT\Context;
use PHPCompiler\JIT\Variable;

use PHPLLVM\Value;


class strlen extends Internal {

    public function execute(Frame $frame): void {
        if (count($frame->calledArgs) !== 1) {
            throw new \LogicException("Expecting exactly a single argument to strlen()");
        }
        $var = $frame->calledArgs[0];
        if (!is_null($frame->returnVar)) {
            $frame->returnVar->int(strlen($var->toString()));
        }
    }

    public Context $context;

    public function call(Context $context, Variable ... $args): Value {
        $this->context = $context;
        if (count($args) !== 1) {
            throw new \LogicException('Too few args passed to strlen()');
        }
        $argValue = $context->helper->loadValue($args[0]);
        switch ($args[0]->type) {
            case Variable::TYPE_STRING:
                $offset = $this->context->structFieldMap[$argValue->typeOf()->getElementType()->getName()]['length'];
                    $result = $this->context->builder->load(
                        $this->context->builder->structGep($argValue, $offset)
                    );
    
                return $result;
        }
        throw new \LogicException('Non-implemented type handled: ' . $args[0]->type);
    }

}