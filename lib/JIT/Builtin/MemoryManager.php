<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/lib/JIT/Builtin/MemoryManager.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

use PHPLLVM;

abstract class MemoryManager extends Builtin
{
    public function register(): void
    {
        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
            $this->context->getTypeFromString('int8*'),
            false,
            $this->context->getTypeFromString('size_t')
        );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction(
            '__mm__malloc',
            $fntype___cfcd208495d565ef66e7dff9f98764da
        );
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            PHPLLVM\Attribute::INDEX_FUNCTION,
            $this->context->attributes['alwaysinline']
        );

        $this->context->registerFunction(
            '__mm__malloc',
            $fn___cfcd208495d565ef66e7dff9f98764da
        );

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
            $this->context->getTypeFromString('int8*'),
            false,
            $this->context->getTypeFromString('int8*'),
            $this->context->getTypeFromString('size_t')
        );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction(
            '__mm__realloc',
            $fntype___cfcd208495d565ef66e7dff9f98764da
        );
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            PHPLLVM\Attribute::INDEX_FUNCTION,
            $this->context->attributes['alwaysinline']
        );

        $this->context->registerFunction(
            '__mm__realloc',
            $fn___cfcd208495d565ef66e7dff9f98764da
        );

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
            $this->context->getTypeFromString('void'),
            false,
            $this->context->getTypeFromString('int8*')
        );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction(
            '__mm__free',
            $fntype___cfcd208495d565ef66e7dff9f98764da
        );
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(
            PHPLLVM\Attribute::INDEX_FUNCTION,
            $this->context->attributes['alwaysinline']
        );

        $this->context->registerFunction(
            '__mm__free',
            $fn___cfcd208495d565ef66e7dff9f98764da
        );
    }

    public function malloc(PHPLLVM\Type $type): PHPLLVM\Value
    {
        if ($type instanceof \PHPLLVM\Type) {
            $type = $type;
        } elseif ($type instanceof \PHPLLVM\Value) {
            $type = $type->typeOf();
        } else {
            throw new \LogicException(
                "Attempt to call sizeof on non-PHPLLVM type/value"
            );
        }
        $size = $this->context->builder->ptrToInt(
            $this->context->builder->gep(
                $type->pointerType(0)->constNull(),
                $this->context->context->int32Type()->constInt(1, false)
            ),
            $this->context->getTypeFromString('size_t')
        );
        $ptr = $this->context->builder->call(
            $this->context->lookupFunction('__mm__malloc'),
            $size
        );

        return $this->context->builder->pointerCast(
            $ptr,
            $type->pointerType(0)
        );
    }

    public function mallocWithExtra(
        PHPLLVM\Type $type,
        PHPLLVM\Value $extra
    ): PHPLLVM\Value {
        if ($type instanceof \PHPLLVM\Type) {
            $type = $type;
        } elseif ($type instanceof \PHPLLVM\Value) {
            $type = $type->typeOf();
        } else {
            throw new \LogicException(
                "Attempt to call sizeof on non-PHPLLVM type/value"
            );
        }
        $size = $this->context->builder->ptrToInt(
            $this->context->builder->gep(
                $type->pointerType(0)->constNull(),
                $this->context->context->int32Type()->constInt(1, false)
            ),
            $this->context->getTypeFromString('size_t')
        );
        $__right = $this->context->builder->intCast($extra, $size->typeOf());

        $size = $this->context->builder->addNoUnsignedWrap($size, $__right);
        $ptr = $this->context->builder->call(
            $this->context->lookupFunction('__mm__malloc'),
            $size
        );

        return $this->context->builder->pointerCast(
            $ptr,
            $type->pointerType(0)
        );
    }

    public function realloc(
        PHPLLVM\Value $value,
        PHPLLVM\Value $extra
    ): PHPLLVM\Value {
        $type = $value->typeOf()->getElementType();
        if ($type instanceof \PHPLLVM\Type) {
            $type = $type;
        } elseif ($type instanceof \PHPLLVM\Value) {
            $type = $type->typeOf();
        } else {
            throw new \LogicException(
                "Attempt to call sizeof on non-PHPLLVM type/value"
            );
        }
        $size = $this->context->builder->ptrToInt(
            $this->context->builder->gep(
                $type->pointerType(0)->constNull(),
                $this->context->context->int32Type()->constInt(1, false)
            ),
            $this->context->getTypeFromString('size_t')
        );
        $__right = $this->context->builder->intCast($extra, $size->typeOf());

        $allocSize = $this->context->builder->addNoUnsignedWrap(
            $size,
            $__right
        );
        $void = $this->context->builder->pointerCast(
            $value,
            $this->context->getTypeFromString('int8*')
        );
        $ptr = $this->context->builder->call(
            $this->context->lookupFunction('__mm__realloc'),
            $void,
            $allocSize
        );

        return $this->context->builder->pointerCast(
            $ptr,
            $type->pointerType(0)
        );
    }

    public function free(PHPLLVM\Value $value): void
    {
        $void = $this->context->builder->pointerCast(
            $value,
            $this->context->getTypeFromString('int8*')
        );
        $this->context->builder->call(
            $this->context->lookupFunction('__mm__free'),
            $void
        );
    }
}
