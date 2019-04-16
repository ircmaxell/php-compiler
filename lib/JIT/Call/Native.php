<?php

# This file is generated, changes you make will be lost.
# Make your changes in /compiler/lib/JIT/Call/Native.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Call;

use PHPCompiler\JIT\Context;
use PHPCompiler\JIT\Call;
use PHPCompiler\JIT\Variable;

use PHPLLVM\Value;

class Native implements Call {

    public Value $function;
    public string $name;
    public array $argTypes;

    public function __construct(Value $function, string $name, array $argTypes) {
        $this->function = $function;
        $this->name = $name;
        $this->argTypes = $argTypes;
    }

    public function call(Context $context, Variable ... $args): Value {
        $argValues = [];
        foreach ($args as $index => $arg) {
            $argValues[] = $this->compileArg($context, $arg, $index);
        }
        return $context->builder->call(
            $this->function,
            ...$argValues
        );
    }

    protected function compileArg(Context $context, Variable $arg, int $argNum): Value {
        $type = $this->argTypes[$argNum];
        $typeName = $context->getStringFromType($type);
        $value = $context->helper->loadValue($arg);
        switch ($typeName) {
            case '__string__*':
                switch ($arg->type) {
                    case Variable::TYPE_STRING:
                        return $value;
                    case Variable::TYPE_VALUE:
                        $str = $this->context->builder->call(
                        $this->context->lookupFunction('__value__readString') , 
                        $value
                        
                    );
    
                        return $str;
                }
                break;
            case '__value__':
                switch ($arg->type) {
                    case Variable::TYPE_VALUE:
                        return $value;
                }
                break;
            case 'int64':
                switch ($arg->type) {
                    case Variable::TYPE_NATIVE_LONG:
                        return $value;
                    case Variable::TYPE_VALUE:
                        $int = $this->context->builder->call(
                        $this->context->lookupFunction('__value__readLong') , 
                        $value
                        
                    );
    
                        return $int;
                }
                break;
            case 'double':
                switch ($arg->type) {
                    case Variable::TYPE_NATIVE_DOUBLE:
                        return $value;
                    case Variable::TYPE_NATIVE_LONG:
                        $__type = $this->context->context->doubleType();
                        
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $value;
                    switch ($__kind) {
                        case PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $double = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $double = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $double = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $double = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                case PHPLLVM\Type::KIND_POINTER:
                                    $double = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $double = $__type->constReal($value);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $double = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case PHPLLVM\Type::KIND_DOUBLE:
                                    $double = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case PHPLLVM\Type::KIND_ARRAY:
                        case PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $double = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case PHPLLVM\Type::KIND_INTEGER:
                                    $double = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case PHPLLVM\Type::KIND_POINTER:
                                    $double = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    
                        return $double;
                    case Variable::TYPE_VALUE:
                        $double = $this->context->builder->call(
                        $this->context->lookupFunction('__value__readDouble') , 
                        $value
                        
                    );
    
                        return $double;
                }
                break;
        }
        throw new \LogicException("Unsupported cast for arg type $typeName from " . Variable::getStringType($arg->type));
    }

}