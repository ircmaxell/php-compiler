<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/lib/JIT/Builtin/Output.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

class Output extends Builtin
{
    public function register(): void
    {
        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
            $this->context->getTypeFromString('int32'),
            true,
            $this->context->getTypeFromString('char*')
        );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction(
            'printf',
            $fntype___cfcd208495d565ef66e7dff9f98764da
        );

        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            0 + 1,
            $this->context->attributes['readonly'],
            0
        );
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            0 + 1,
            $this->context->attributes['nocapture'],
            0
        );

        $this->context->registerFunction(
            'printf',
            $fn___cfcd208495d565ef66e7dff9f98764da
        );

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
            $this->context->getTypeFromString('int32'),
            true,
            $this->context->getTypeFromString('char*'),
            $this->context->getTypeFromString('char*')
        );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction(
            'sprintf',
            $fntype___cfcd208495d565ef66e7dff9f98764da
        );

        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            1 + 1,
            $this->context->attributes['readonly'],
            0
        );
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            1 + 1,
            $this->context->attributes['nocapture'],
            0
        );

        $this->context->registerFunction(
            'sprintf',
            $fn___cfcd208495d565ef66e7dff9f98764da
        );

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
            $this->context->getTypeFromString('int32'),
            true,
            $this->context->getTypeFromString('char*'),
            $this->context->getTypeFromString('size_t'),
            $this->context->getTypeFromString('char*')
        );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction(
            'snprintf',
            $fntype___cfcd208495d565ef66e7dff9f98764da
        );

        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            2 + 1,
            $this->context->attributes['readonly'],
            0
        );
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            2 + 1,
            $this->context->attributes['nocapture'],
            0
        );

        $this->context->registerFunction(
            'snprintf',
            $fn___cfcd208495d565ef66e7dff9f98764da
        );
    }
}
