<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\types;

use PHPCompiler\Func\Internal;
use PHPCompiler\Frame;
use PHPCompiler\VM\Variable;
use PHPCompiler\JIT\Context;
use PHPCompiler\JIT\Variable as JITVariable;

use PHPLLVM\Value;

class is_type extends Internal {

    private int $type;

    public function __construct(string $name, int $type) {
        parent::__construct($name);
        $this->type = $type;
    }

    public function execute(Frame $frame): void {
        if (count($frame->calledArgs) !== 1) {
            throw new \LogicException("Expecting exactly a single argument to {$this->name}()");
        }
        $var = $frame->calledArgs[0];
        if (!is_null($frame->returnVar)) {
            $frame->returnVar->bool($var->type === $this->type);
        }
    }

    public Context $context;

    public function call(Context $context, JITVariable ... $args): Value {
        $this->context = $context;
        if (count($args) !== 1) {
            throw new \LogicException('Too few args passed to ' . $this->name . '()');
        }
        switch ($args[0]->type) {
            case JITVariable::TYPE_NATIVE_LONG:
                return $this->context->constantFromBool($this->type === Variable::TYPE_INTEGER);
            case JITVariable::TYPE_NATIVE_DOUBLE:
                return $this->context->constantFromBool($this->type === Variable::TYPE_FLOAT);
            case JITVariable::TYPE_STRING:
                return $this->context->constantFromBool($this->type === Variable::TYPE_STRING);
            default:
                throw new \LogicException('Non-implemented type handled for ' . $this->name . '(): ' . JITVariable::getStringType($args[0]->type));
        }
    }

}