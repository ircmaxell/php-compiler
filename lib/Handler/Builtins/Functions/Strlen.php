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

class Strlen extends Functions {

    

    public function execute(Frame $frame): void {
        $var = $frame->calledArgs[0];
        if (!is_null($frame->returnVar)) {
            $frame->returnVar->int(strlen($var->toString()));
        }
    }

    public function getName(): string {
        return 'strlen';
    }

    public function getReturnType(): string {
        return 'long long';
    }
    public function getParamTypes(): array {
        return [
            '__string__*',
        ];
    }

    public function implement(\gcc_jit_function_ptr $func, \gcc_jit_param_ptr ...$params): void {
        $block = \gcc_jit_function_new_block($func, 'main');
        \gcc_jit_block_end_with_return(
            $block,
            $this->jitContext->location(),
            $this->jitContext->helper->call(
                '__string__strlen',
                $params[0]->asRValue()
            )
        );
    }

}