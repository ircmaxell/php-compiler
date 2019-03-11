<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\types;

use PHPCompiler\Func\Internal\JITInlined;
use PHPCompiler\Frame;
use PHPCompiler\VM\Variable;
use PHPCompiler\JIT\Variable as JITVariable;
use PHPCompiler\JIT;
use PHPCompiler\JIT\Func as JITFunc;

class is_type extends JITInlined {

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

    public function call(\gcc_jit_rvalue_ptr ... $args): \gcc_jit_rvalue_ptr {
        if (count($args) !== 1) {
            throw new \LogicException('Too few args passed to ' . $this->name . '()');
        }
        $type = $this->jit->context->getStringFromType(\gcc_jit_rvalue_get_type($args[0]));
        switch ($type) {
            case 'long long':
                return $this->jit->context->constantFromBool($this->type === Variable::TYPE_INTEGER);
            case '__string__*':
                return $this->jit->context->constantFromBool($this->type === Variable::TYPE_STRING);
            case '__value__':
                return \gcc_jit_context_new_comparison(
                    $this->jit->context->context,
                    $this->jit->context->location(),
                    \GCC_JIT_COMPARISON_EQ,
                    $this->jit->context->type->value->readType($args[0]),
                    $this->jit->context->constantFromInteger(JITVariable::fromVMVariable($this->type), 'unsigned char')
                );
            default:
                throw new \LogicException('Non-implemented type handled for ' . $this->name . '(): ' . $type);
        }
    }

}