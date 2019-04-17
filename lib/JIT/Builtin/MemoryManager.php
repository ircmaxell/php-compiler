<?php

# This file is generated, changes you make will be lost.
# Make your changes in /compiler/lib/JIT/Builtin/MemoryManager.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

use PHPLLVM;

abstract class MemoryManager extends Builtin {

    public function register(): void {
        $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('int8*'),
                false , 
                $this->context->getTypeFromString('size_t')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__mm__malloc', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__mm__malloc', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('int8*'),
                false , 
                $this->context->getTypeFromString('int8*')
                , $this->context->getTypeFromString('size_t')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__mm__realloc', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            
            $this->context->registerFunction('__mm__realloc', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('int8*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__mm__free', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__mm__free', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    
    }

    public function malloc(PHPLLVM\Type $type): PHPLLVM\Value {
        if ($type instanceof \PHPLLVM\Type) {
                        $type = $type;
                    } elseif ($type instanceof \PHPLLVM\Value) {
                        $type = $type->typeOf();
                    } else {
                        throw new \LogicException("Attempt to call sizeof on non-PHPLLVM type/value");
                    }
                    $size = $this->context->builder->ptrToInt(
                        $this->context->builder->gep(
                            $type->pointerType(0)->constNull(),
                            $this->context->context->int32Type()->constInt(1, false)
                        ),
                        $this->context->getTypeFromString('size_t')
                    );
    $ptr = $this->context->builder->call(
                        $this->context->lookupFunction('__mm__malloc') , 
                        $size
                        
                    );
    
        return $this->context->builder->pointerCast($ptr, $type->pointerType(0));
    }

    public function mallocWithExtra(PHPLLVM\Type $type, PHPLLVM\Value $extra): PHPLLVM\Value {
        if ($type instanceof \PHPLLVM\Type) {
                        $type = $type;
                    } elseif ($type instanceof \PHPLLVM\Value) {
                        $type = $type->typeOf();
                    } else {
                        throw new \LogicException("Attempt to call sizeof on non-PHPLLVM type/value");
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
                        $this->context->lookupFunction('__mm__malloc') , 
                        $size
                        
                    );
    
        return $this->context->builder->pointerCast($ptr, $type->pointerType(0));
    }

    public function realloc(PHPLLVM\Value $value, PHPLLVM\Value $extra): PHPLLVM\Value {
        $type = $value->typeOf()->getElementType();
        if ($type instanceof \PHPLLVM\Type) {
                        $type = $type;
                    } elseif ($type instanceof \PHPLLVM\Value) {
                        $type = $type->typeOf();
                    } else {
                        throw new \LogicException("Attempt to call sizeof on non-PHPLLVM type/value");
                    }
                    $size = $this->context->builder->ptrToInt(
                        $this->context->builder->gep(
                            $type->pointerType(0)->constNull(),
                            $this->context->context->int32Type()->constInt(1, false)
                        ),
                        $this->context->getTypeFromString('size_t')
                    );
    $__right = $this->context->builder->intCast($extra, $size->typeOf());
                            
                            
                        

                        

                        

                        

                        $allocSize = $this->context->builder->addNoUnsignedWrap($size, $__right);
    $__type = $this->context->getTypeFromString('int8*');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $value;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $void = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $void = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $void = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $void = $this->context->builder->fpToUi($__value, $__type);
                                    
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $void = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $void = $__type->constReal($value);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $void = $this->context->builder->uiToFp($__value, $__type);
                                    
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $void = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $void = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $void = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $void = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $ptr = $this->context->builder->call(
                        $this->context->lookupFunction('__mm__realloc') , 
                        $void
                        , $allocSize
                        
                    );
    
        return $this->context->builder->pointerCast($ptr, $type->pointerType(0));
    }

    public function free(PHPLLVM\Value $value): void {
        $__type = $this->context->getTypeFromString('int8*');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $value;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $void = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $void = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $void = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $void = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $void = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $void = $__type->constReal($value);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $void = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $void = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $void = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $void = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $void = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $this->context->builder->call(
                    $this->context->lookupFunction('__mm__free') , 
                    $void
                    
                );
    
    }

}