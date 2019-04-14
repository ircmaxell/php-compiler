<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

// Make your changes in /compiler/script/../lib/JIT/Builtin/Type/Value.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */
namespace PHPCompiler\JIT\Builtin\Type;

use PHPCompiler\JIT\Builtin\Type;
use PHPCompiler\JIT\Variable;

use PHPLLVM;

class Value extends Type
{
    public function register(): void
    {
        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__value__');
        // declare first so recursive structs are possible :)
        $this->context->registerType('__value__', $struct___cfcd208495d565ef66e7dff9f98764da);
        $this->context->registerType('__value__'.'*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
        $this->context->registerType('__value__'.'**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
        $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false,  // packed
                $this->context->getTypeFromString('int8'), $this->context->getTypeFromString('int8[8]')

            );
        $this->context->structFieldMap['__value__'] = [
            'type' => 0, 'value' => 1,

        ];

        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__value__value');
        // declare first so recursive structs are possible :)
        $this->context->registerType('__value__value', $struct___cfcd208495d565ef66e7dff9f98764da);
        $this->context->registerType('__value__value'.'*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
        $this->context->registerType('__value__value'.'**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
        $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false,  // packed
                $this->context->getTypeFromString('__ref__'), $this->context->getTypeFromString('__value__')

            );
        $this->context->structFieldMap['__value__value'] = [
            'ref' => 0, 'value' => 1,

        ];

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false,
                $this->context->getTypeFromString('__value__*')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__valueDelref', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__valueDelref', $fn___cfcd208495d565ef66e7dff9f98764da);

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('__value__*'),
                false,
                $this->context->getTypeFromString('__value__*')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__toNumeric', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__toNumeric', $fn___cfcd208495d565ef66e7dff9f98764da);

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('int64'),
                false,
                $this->context->getTypeFromString('__value__*')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__readLong', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__readLong', $fn___cfcd208495d565ef66e7dff9f98764da);

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false,
                $this->context->getTypeFromString('__value__*'), $this->context->getTypeFromString('int64')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__writeLong', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__writeLong', $fn___cfcd208495d565ef66e7dff9f98764da);

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('double'),
                false,
                $this->context->getTypeFromString('__value__*')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__readDouble', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__readDouble', $fn___cfcd208495d565ef66e7dff9f98764da);

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false,
                $this->context->getTypeFromString('__value__*'), $this->context->getTypeFromString('double')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__writeDouble', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__writeDouble', $fn___cfcd208495d565ef66e7dff9f98764da);

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('__string__*'),
                false,
                $this->context->getTypeFromString('__value__*')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__readString', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__readString', $fn___cfcd208495d565ef66e7dff9f98764da);

        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false,
                $this->context->getTypeFromString('__value__*'), $this->context->getTypeFromString('__string__*')

            );
        $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__value__writeString', $fntype___cfcd208495d565ef66e7dff9f98764da);
        $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);

        $this->context->registerFunction('__value__writeString', $fn___cfcd208495d565ef66e7dff9f98764da);
    }

    public function implement(): void
    {
        // $this->implementValueToNumeric();
        // $this->implementValueReadLong();
        // $this->implementValueWriteLong();
        // $this->implementValueReadDouble();
        // $this->implementValueWriteDouble();
        // $this->implementValueDelref();
    }

    public function initialize(): void
    {
    }

    public function implementValueWriteLong(): void
    {
        $fn___9bf31c7ff062936a96d3c8bd1f8f2ff3 = $this->context->lookupFunction('__value__writeLong');
        $block___9bf31c7ff062936a96d3c8bd1f8f2ff3 = $fn___9bf31c7ff062936a96d3c8bd1f8f2ff3->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___9bf31c7ff062936a96d3c8bd1f8f2ff3);
        $value = $fn___9bf31c7ff062936a96d3c8bd1f8f2ff3->getParam(0);
        $long = $fn___9bf31c7ff062936a96d3c8bd1f8f2ff3->getParam(1);

        $this->context->builder->call(
                    $this->context->lookupFunction('__value__valueDelref'),
                    $value

                );
        $__type = $this->context->getTypeFromString('int8');

        $__kind = $__type->getKind();
        $__value = Variable::TYPE_NATIVE_LONG;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $type = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $type = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $type = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $type = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $type = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $type = $__type->constReal(Variable::TYPE_NATIVE_LONG);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $type = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $type = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $type = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $type = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $type = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $type = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $this->context->builder->store(
                    $type,
                    $this->context->builder->structGep($value, $offset)
                );
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('int64*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $resultPtr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $resultPtr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $resultPtr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $resultPtr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $resultPtr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $resultPtr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $resultPtr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $__type = $this->context->getTypeFromString('int32');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $offset = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $offset = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $offset = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $offset = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $offset = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $offset = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $offset = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $offset = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->store($long, $this->context->builder->gep(
                    $resultPtr,
                    //$this->context->context->int32Type()->constInt(0, false),
                    //$this->context->context->int32Type()->constInt(0, false),
                    $offset
                ));
        $this->context->builder->returnVoid();

        $this->context->builder->clearInsertionPosition();
    }

    public function implementValueWriteDouble(): void
    {
        $fn___b6d767d2f8ed5d21a44b0e5886680cb9 = $this->context->lookupFunction('__value__writeDouble');
        $block___b6d767d2f8ed5d21a44b0e5886680cb9 = $fn___b6d767d2f8ed5d21a44b0e5886680cb9->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___b6d767d2f8ed5d21a44b0e5886680cb9);
        $value = $fn___b6d767d2f8ed5d21a44b0e5886680cb9->getParam(0);
        $double = $fn___b6d767d2f8ed5d21a44b0e5886680cb9->getParam(1);

        $this->context->builder->call(
                    $this->context->lookupFunction('__value__valueDelref'),
                    $value

                );
        $__type = $this->context->getTypeFromString('int8');

        $__kind = $__type->getKind();
        $__value = Variable::TYPE_NATIVE_DOUBLE;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $type = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $type = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $type = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $type = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $type = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $type = $__type->constReal(Variable::TYPE_NATIVE_DOUBLE);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $type = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $type = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $type = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $type = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $type = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $type = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $this->context->builder->store(
                    $type,
                    $this->context->builder->structGep($value, $offset)
                );
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('double*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $resultPtr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $resultPtr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $resultPtr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $resultPtr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $resultPtr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $resultPtr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $resultPtr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $__type = $this->context->getTypeFromString('int32');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $offset = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $offset = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $offset = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $offset = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $offset = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $offset = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $offset = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $offset = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->store($double, $this->context->builder->gep(
                    $resultPtr,
                    //$this->context->context->int32Type()->constInt(0, false),
                    //$this->context->context->int32Type()->constInt(0, false),
                    $offset
                ));
        $this->context->builder->returnVoid();

        $this->context->builder->clearInsertionPosition();
    }

    public function implementValueWriteString(): void
    {
        $fn___33e75ff09dd601bbe69f351039152189 = $this->context->lookupFunction('__value__writeString');
        $block___33e75ff09dd601bbe69f351039152189 = $fn___33e75ff09dd601bbe69f351039152189->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___33e75ff09dd601bbe69f351039152189);
        $value = $fn___33e75ff09dd601bbe69f351039152189->getParam(0);
        $string = $fn___33e75ff09dd601bbe69f351039152189->getParam(1);

        $this->context->builder->call(
                    $this->context->lookupFunction('__value__valueDelref'),
                    $value

                );
        $__type = $this->context->getTypeFromString('int8');

        $__kind = $__type->getKind();
        $__value = Variable::TYPE_STRING;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $type = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $type = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $type = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $type = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $type = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $type = $__type->constReal(Variable::TYPE_STRING);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $type = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $type = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $type = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $type = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $type = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $type = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $this->context->builder->store(
                    $type,
                    $this->context->builder->structGep($value, $offset)
                );
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $this->context->builder->store(
                    $string,
                    $this->context->builder->structGep($value, $offset)
                );
        $this->context->builder->returnVoid();

        $this->context->builder->clearInsertionPosition();
    }

    protected function implementValueDelref(): void
    {
        $fn___c4ca4238a0b923820dcc509a6f75849b = $this->context->lookupFunction('__value__valueDelref');
        $block___c4ca4238a0b923820dcc509a6f75849b = $fn___c4ca4238a0b923820dcc509a6f75849b->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___c4ca4238a0b923820dcc509a6f75849b);
        $value = $fn___c4ca4238a0b923820dcc509a6f75849b->getParam(0);

        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $oldType = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__type = $this->context->getTypeFromString('int8');

        $__kind = $__type->getKind();
        $__value = Variable::IS_REFCOUNTED;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $mask = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $mask = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $mask = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $mask = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $mask = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $mask = $__type->constReal(Variable::IS_REFCOUNTED);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $mask = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $mask = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $mask = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $mask = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $mask = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $mask = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $__right = $this->context->builder->intCast($mask, $oldType->typeOf());

        $isCounted = $this->context->builder->bitwiseAnd($oldType, $__right);
        $bool = $this->context->castToBool($isCounted);
        $prev = $this->context->builder->getInsertBlock();
        $ifBlock = $prev->insertBasicBlock('ifBlock');
        $prev->moveBefore($ifBlock);

        $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
        $this->context->builder->branchIf($bool, $ifBlock, $tmp);

        $this->context->builder->positionAtEnd($ifBlock);
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('__ref__virtual*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $virtual = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $virtual = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $virtual = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $virtual = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $virtual = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $virtual = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $virtual = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $virtual = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $virtual = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $virtual = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->call(
                    $this->context->lookupFunction('__ref__delref'),
                    $virtual

                );

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($endBlock));
        }

        $this->context->builder->positionAtEnd(array_pop($endBlock));
        $this->context->builder->returnVoid();

        $this->context->builder->clearInsertionPosition();
    }

    protected function implementValueToNumeric(): void
    {
        $fn___a87ff679a2f3e71d9181a67b7542122c = $this->context->lookupFunction('__value__toNumeric');
        $block___a87ff679a2f3e71d9181a67b7542122c = $fn___a87ff679a2f3e71d9181a67b7542122c->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___a87ff679a2f3e71d9181a67b7542122c);
        $value = $fn___a87ff679a2f3e71d9181a67b7542122c->getParam(0);

        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $type = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__switches[] = $__switch = new \StdClass();
        $__switch->type = $type->typeOf();
        $__prev = $this->context->builder->getInsertBlock();
        $__switch->default = $__prev->insertBasicBlock('default');
        $__prev->moveBefore($__switch->default);
        $__switch->end = $__switch->default->insertBasicBlock('end');
        $__switch->endIsUsed = false;
        $__switch->numCases = 0;
        ++$__switch->numCases;
        ++$__switch->numCases;
        ++$__switch->numCases;

        $__switch->switch = $this->context->builder->branchSwitch($type, $__switch->default, $__switch->numCases);
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_NATIVE_LONG)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_NATIVE_LONG, false), $__case);
        } elseif (Variable::TYPE_NATIVE_LONG instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_NATIVE_LONG, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $__type = $this->context->getTypeFromString('__ref__virtual*');

        $__kind = $__type->getKind();
        $__value = $value;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $var = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $var = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $var = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $var = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $var = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $var = $__type->constReal($value);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $var = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $var = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $var = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $var = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $var = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $var = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->call(
                    $this->context->lookupFunction('__ref__addref'),
                    $var

                );
        $this->context->builder->returnValue($value);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_NATIVE_DOUBLE)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_NATIVE_DOUBLE, false), $__case);
        } elseif (Variable::TYPE_NATIVE_DOUBLE instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_NATIVE_DOUBLE, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $__type = $this->context->getTypeFromString('__ref__virtual*');

        $__kind = $__type->getKind();
        $__value = $value;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $var = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $var = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $var = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $var = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $var = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $var = $__type->constReal($value);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $var = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $var = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $var = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $var = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $var = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $var = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->call(
                    $this->context->lookupFunction('__ref__addref'),
                    $var

                );
        $this->context->builder->returnValue($value);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_VALUE)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_VALUE, false), $__case);
        } elseif (Variable::TYPE_VALUE instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_VALUE, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $var = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__type = $this->context->getTypeFromString('__value__value*');

        $__kind = $__type->getKind();
        $__value = $var;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $ptr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $ptr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $ptr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $ptr = $__type->constReal($var);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $ptr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $ptr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $ptr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $ptr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $offset = $this->context->structFieldMap[$ptr->typeOf()->getElementType()->getName()]['value'];
        $num = $this->context->builder->structGep($ptr, $offset);
        $result = $this->context->builder->call(
                        $this->context->lookupFunction('__value__toNumeric'),
                        $num

                    );
        $__right = $this->context->builder->intCast($num, $result->typeOf());

        $test = $this->context->builder->icmp(PHPLLVM\Builder::INT_EQ, $result, $__right);
        $bool = $this->context->castToBool($test);
        $prev = $this->context->builder->getInsertBlock();
        $ifBlock = $prev->insertBasicBlock('ifBlock');
        $prev->moveBefore($ifBlock);

        $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
        $this->context->builder->branchIf($bool, $ifBlock, $tmp);

        $this->context->builder->positionAtEnd($ifBlock);
        $__type = $this->context->getTypeFromString('__ref__virtual*');

        $__kind = $__type->getKind();
        $__value = $var;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $virtual = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $virtual = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $virtual = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $virtual = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $virtual = $__type->constReal($var);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $virtual = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $virtual = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $virtual = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $virtual = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $virtual = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->call(
                    $this->context->lookupFunction('__ref__addref'),
                    $virtual

                );

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($endBlock));
        }

        $this->context->builder->positionAtEnd(array_pop($endBlock));
        $this->context->builder->returnValue($result);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }

        $this->context->builder->positionAtEnd(end($__switches)->default);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__switch = array_pop($__switches);
        if ($__switch->endIsUsed) {
            $this->context->builder->positionAtEnd($__switch->end);
        } else {
            $__switch->end->remove();
        }
        $type = $this->context->getTypeFromString('__value__');
        $var = $this->context->builder->alloca($type);
        $__type = $this->context->getTypeFromString('int64');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $tmp = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $tmp = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $tmp = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $tmp = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $tmp = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $tmp = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $tmp = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $tmp = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $tmp = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $tmp = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $tmp = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $tmp = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->call(
                    $this->context->lookupFunction('__value__writeLong'),
                    $var, $tmp

                );
        $this->context->builder->returnValue($var);

        $this->context->builder->clearInsertionPosition();
    }

    protected function implementValueReadLong(): void
    {
        $fn___d3d9446802a44259755d38e6d163e820 = $this->context->lookupFunction('__value__readLong');
        $block___d3d9446802a44259755d38e6d163e820 = $fn___d3d9446802a44259755d38e6d163e820->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___d3d9446802a44259755d38e6d163e820);
        $value = $fn___d3d9446802a44259755d38e6d163e820->getParam(0);

        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $type = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__switches[] = $__switch = new \StdClass();
        $__switch->type = $type->typeOf();
        $__prev = $this->context->builder->getInsertBlock();
        $__switch->default = $__prev->insertBasicBlock('default');
        $__prev->moveBefore($__switch->default);
        $__switch->end = $__switch->default->insertBasicBlock('end');
        $__switch->endIsUsed = false;
        $__switch->numCases = 0;
        ++$__switch->numCases;
        ++$__switch->numCases;
        ++$__switch->numCases;

        $__switch->switch = $this->context->builder->branchSwitch($type, $__switch->default, $__switch->numCases);
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_NATIVE_LONG)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_NATIVE_LONG, false), $__case);
        } elseif (Variable::TYPE_NATIVE_LONG instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_NATIVE_LONG, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('int64*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $resultPtr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $resultPtr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $resultPtr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $resultPtr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $resultPtr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $resultPtr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $resultPtr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $__type = $this->context->getTypeFromString('int32');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $offset = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $offset = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $offset = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $offset = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $offset = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $offset = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $offset = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $offset = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $result = $this->context->builder->load($this->context->builder->gep(
                        $resultPtr,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $offset
                    ));
        $this->context->builder->returnValue($result);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_NATIVE_DOUBLE)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_NATIVE_DOUBLE, false), $__case);
        } elseif (Variable::TYPE_NATIVE_DOUBLE instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_NATIVE_DOUBLE, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('double*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $resultPtr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $resultPtr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $resultPtr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $resultPtr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $resultPtr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $resultPtr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $resultPtr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $__type = $this->context->getTypeFromString('int32');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $offset = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $offset = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $offset = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $offset = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $offset = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $offset = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $offset = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $offset = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $result = $this->context->builder->load($this->context->builder->gep(
                        $resultPtr,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $offset
                    ));
        $__type = $this->context->getTypeFromString('int64');

        $__kind = $__type->getKind();
        $__value = $result;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $return = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $return = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $return = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $return = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $return = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $return = $__type->constReal($result);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $return = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $return = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $return = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $return = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $return = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $return = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->returnValue($return);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_VALUE)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_VALUE, false), $__case);
        } elseif (Variable::TYPE_VALUE instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_VALUE, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $var = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__type = $this->context->getTypeFromString('__value__value*');

        $__kind = $__type->getKind();
        $__value = $var;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $ptr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $ptr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $ptr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $ptr = $__type->constReal($var);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $ptr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $ptr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $ptr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $ptr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $offset = $this->context->structFieldMap[$var->typeOf()->getElementType()->getName()]['value'];
        $varPtr = $this->context->builder->structGep($var, $offset);
        $result = $this->context->builder->call(
                        $this->context->lookupFunction('__value__readLong'),
                        $varPtr

                    );
        $this->context->builder->returnValue($result);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }

        $this->context->builder->positionAtEnd(end($__switches)->default);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__switch = array_pop($__switches);
        if ($__switch->endIsUsed) {
            $this->context->builder->positionAtEnd($__switch->end);
        } else {
            $__switch->end->remove();
        }
        $__type = $this->context->getTypeFromString('int64');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $result = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $result = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $result = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $result = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $result = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $result = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $result = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $result = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $result = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $result = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->returnValue($result);

        $this->context->builder->clearInsertionPosition();
    }

    protected function implementValueReadDouble(): void
    {
        $fn___70efdf2ec9b086079795c442636b55fb = $this->context->lookupFunction('__value__readDouble');
        $block___70efdf2ec9b086079795c442636b55fb = $fn___70efdf2ec9b086079795c442636b55fb->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___70efdf2ec9b086079795c442636b55fb);
        $value = $fn___70efdf2ec9b086079795c442636b55fb->getParam(0);

        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $type = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__switches[] = $__switch = new \StdClass();
        $__switch->type = $type->typeOf();
        $__prev = $this->context->builder->getInsertBlock();
        $__switch->default = $__prev->insertBasicBlock('default');
        $__prev->moveBefore($__switch->default);
        $__switch->end = $__switch->default->insertBasicBlock('end');
        $__switch->endIsUsed = false;
        $__switch->numCases = 0;
        ++$__switch->numCases;
        ++$__switch->numCases;
        ++$__switch->numCases;

        $__switch->switch = $this->context->builder->branchSwitch($type, $__switch->default, $__switch->numCases);
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_NATIVE_LONG)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_NATIVE_LONG, false), $__case);
        } elseif (Variable::TYPE_NATIVE_LONG instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_NATIVE_LONG, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('int64*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $resultPtr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $resultPtr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $resultPtr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $resultPtr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $resultPtr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $resultPtr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $resultPtr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $__type = $this->context->getTypeFromString('int32');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $offset = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $offset = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $offset = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $offset = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $offset = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $offset = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $offset = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $offset = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $result = $this->context->builder->load($this->context->builder->gep(
                        $resultPtr,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $offset
                    ));
        $__type = $this->context->context->doubleType();

        $__kind = $__type->getKind();
        $__value = $result;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $return = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $return = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $return = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $return = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $return = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $return = $__type->constReal($result);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $return = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $return = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $return = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $return = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $return = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $return = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->returnValue($return);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_NATIVE_DOUBLE)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_NATIVE_DOUBLE, false), $__case);
        } elseif (Variable::TYPE_NATIVE_DOUBLE instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_NATIVE_DOUBLE, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('double*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $resultPtr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $resultPtr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $resultPtr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $resultPtr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $resultPtr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $resultPtr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $resultPtr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $__type = $this->context->getTypeFromString('int32');

        $__kind = $__type->getKind();
        $__value = 0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $offset = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $offset = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $offset = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $offset = $__type->constReal(0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $offset = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $offset = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $offset = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $offset = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $offset = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $offset = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $result = $this->context->builder->load($this->context->builder->gep(
                        $resultPtr,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $offset
                    ));
        $this->context->builder->returnValue($result);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_VALUE)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_VALUE, false), $__case);
        } elseif (Variable::TYPE_VALUE instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_VALUE, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $var = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__type = $this->context->getTypeFromString('__value__value*');

        $__kind = $__type->getKind();
        $__value = $var;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $ptr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $ptr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $ptr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $ptr = $__type->constReal($var);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $ptr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $ptr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $ptr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $ptr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $offset = $this->context->structFieldMap[$ptr->typeOf()->getElementType()->getName()]['value'];
        $varPtr = $this->context->builder->structGep($ptr, $offset);
        $result = $this->context->builder->call(
                        $this->context->lookupFunction('__value__readDouble'),
                        $varPtr

                    );
        $this->context->builder->returnValue($result);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }

        $this->context->builder->positionAtEnd(end($__switches)->default);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__switch = array_pop($__switches);
        if ($__switch->endIsUsed) {
            $this->context->builder->positionAtEnd($__switch->end);
        } else {
            $__switch->end->remove();
        }
        $__type = $this->context->context->doubleType();

        $__kind = $__type->getKind();
        $__value = 0.0;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $result = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $result = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $result = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $result = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $result = $__type->constReal(0.0);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $result = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $result = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $result = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $result = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $result = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->returnValue($result);

        $this->context->builder->clearInsertionPosition();
    }

    protected function implementValueReadString(): void
    {
        $fn___1ff1de774005f8da13f42943881c655f = $this->context->lookupFunction('__value__readString');
        $block___1ff1de774005f8da13f42943881c655f = $fn___1ff1de774005f8da13f42943881c655f->appendBasicBlock('main');
        $this->context->builder->positionAtEnd($block___1ff1de774005f8da13f42943881c655f);
        $value = $fn___1ff1de774005f8da13f42943881c655f->getParam(0);

        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
        $type = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );
        $__switches[] = $__switch = new \StdClass();
        $__switch->type = $type->typeOf();
        $__prev = $this->context->builder->getInsertBlock();
        $__switch->default = $__prev->insertBasicBlock('default');
        $__prev->moveBefore($__switch->default);
        $__switch->end = $__switch->default->insertBasicBlock('end');
        $__switch->endIsUsed = false;
        $__switch->numCases = 0;
        ++$__switch->numCases;
        ++$__switch->numCases;

        $__switch->switch = $this->context->builder->branchSwitch($type, $__switch->default, $__switch->numCases);
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_STRING)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_STRING, false), $__case);
        } elseif (Variable::TYPE_STRING instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_STRING, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $ptr = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('__string__*');

        $__kind = $__type->getKind();
        $__value = $ptr;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $resultPtr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $resultPtr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $resultPtr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $resultPtr = $__type->constReal($ptr);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $resultPtr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $resultPtr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $resultPtr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $resultPtr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $resultPtr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $resultPtr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->returnValue($resultPtr);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__case = end($__switches)->default->insertBasicBlock('case_'. 0);
        $this->context->builder->positionAtEnd($__case);
        if (is_int(Variable::TYPE_VALUE)) {
            end($__switches)->switch->addCase(end($__switches)->type->constInt(Variable::TYPE_VALUE, false), $__case);
        } elseif (Variable::TYPE_VALUE instanceof PHPLLVM\Value) {
            end($__switches)->switch->addCase(Variable::TYPE_VALUE, $__case);
        } else {
            throw new \LogicException('Unknown type for switch case');
        }
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
        $var = $this->context->builder->structGep($value, $offset);
        $__type = $this->context->getTypeFromString('__value__value*');

        $__kind = $__type->getKind();
        $__value = $var;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $ptr = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $ptr = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $ptr = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $ptr = $__type->constReal($var);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $ptr = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $ptr = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $ptr = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $ptr = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $ptr = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $ptr = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $offset = $this->context->structFieldMap[$var->typeOf()->getElementType()->getName()]['value'];
        $varPtr = $this->context->builder->load(
                        $this->context->builder->structGep($var, $offset)
                    );
        $result = $this->context->builder->call(
                        $this->context->lookupFunction('__value__readString'),
                        $varPtr

                    );
        $this->context->builder->returnValue($result);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }

        $this->context->builder->positionAtEnd(end($__switches)->default);

        if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
            $this->context->builder->branch(end($__switches)->end);
            end($__switches)->endIsUsed = true;
        }
        $__switch = array_pop($__switches);
        if ($__switch->endIsUsed) {
            $this->context->builder->positionAtEnd($__switch->end);
        } else {
            $__switch->end->remove();
        }
        $__type = $this->context->getTypeFromString('__string__*');

        $__kind = $__type->getKind();
        $__value = null;
        switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (! is_object($__value)) {
                                $result = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $result = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $result = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:

                                    $result = $this->context->builder->fpToSi($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->ptrToInt($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (int, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (! is_object($__value)) {
                                $result = $__type->constReal(null);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:

                                    $result = $this->context->builder->siToFp($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $result = $this->context->builder->fpCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (! is_object($__value)) {
                                // this is very likely very wrong...
                                $result = $__type->constInt($__value, false);

                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $result = $this->context->builder->intToPtr($__value, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    $__tmp = $this->context->builder->bitCast($__value, $this->context->context->int64Type());
                                    $result = $this->context->builder->intToPtr($__tmp, $__type);

                                    break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->pointerCast($__value, $__type);

                                    break;
                                default:
                                    throw new \LogicException('Unknown how to handle type pair (double, '.$__other_type->toString().')');
                            }

                            break;
                        default:
                            throw new \LogicException('Unsupported type cast: '.$__type->toString());
                    }
        $this->context->builder->returnValue($result);

        $this->context->builder->clearInsertionPosition();
    }
}
