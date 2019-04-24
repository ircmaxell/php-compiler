<?php

# This file is generated, changes you make will be lost.
# Make your changes in /compiler/lib/JIT/Builtin/MemoryManager/PHP.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\MemoryManager;

use PHPCompiler\JIT\Builtin\MemoryManager;

class PHP extends MemoryManager {
    public function register(): void {
        parent::register();
        if (\PHP_DEBUG) {
            $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('int8*'),
                false , 
                $this->context->getTypeFromString('size_t')
                , $this->context->getTypeFromString('char*')
                , $this->context->getTypeFromString('uint32')
                , $this->context->getTypeFromString('char*')
                , $this->context->getTypeFromString('uint32')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('_emalloc', $fntype___cfcd208495d565ef66e7dff9f98764da);
            
            
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(1 + 1, $this->context->attributes['readonly'], 0);
                    $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(1 + 1, $this->context->attributes['nocapture'], 0);
                
            
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(3 + 1, $this->context->attributes['readonly'], 0);
                    $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(3 + 1, $this->context->attributes['nocapture'], 0);
                
            
            
            $this->context->registerFunction('_emalloc', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('int8*'),
                false , 
                $this->context->getTypeFromString('int8*')
                , $this->context->getTypeFromString('size_t')
                , $this->context->getTypeFromString('char*')
                , $this->context->getTypeFromString('uint32')
                , $this->context->getTypeFromString('char*')
                , $this->context->getTypeFromString('uint32')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('_erealloc', $fntype___cfcd208495d565ef66e7dff9f98764da);
            
            
            
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(2 + 1, $this->context->attributes['readonly'], 0);
                    $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(2 + 1, $this->context->attributes['nocapture'], 0);
                
            
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(4 + 1, $this->context->attributes['readonly'], 0);
                    $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(4 + 1, $this->context->attributes['nocapture'], 0);
                
            
            
            $this->context->registerFunction('_erealloc', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('int8*')
                , $this->context->getTypeFromString('char*')
                , $this->context->getTypeFromString('uint32')
                , $this->context->getTypeFromString('char*')
                , $this->context->getTypeFromString('uint32')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('_efree', $fntype___cfcd208495d565ef66e7dff9f98764da);
            
            
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(1 + 1, $this->context->attributes['readonly'], 0);
                    $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(1 + 1, $this->context->attributes['nocapture'], 0);
                
            
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(3 + 1, $this->context->attributes['readonly'], 0);
                    $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(3 + 1, $this->context->attributes['nocapture'], 0);
                
            
            
            $this->context->registerFunction('_efree', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    
        } else {
           $fntype___c4ca4238a0b923820dcc509a6f75849b = $this->context->context->functionType(
                $this->context->getTypeFromString('int8*'),
                false , 
                $this->context->getTypeFromString('size_t')
                
            );
            $fn___c4ca4238a0b923820dcc509a6f75849b = $this->context->module->addFunction('_emalloc', $fntype___c4ca4238a0b923820dcc509a6f75849b);
            
            
            
            $this->context->registerFunction('_emalloc', $fn___c4ca4238a0b923820dcc509a6f75849b);
        

        

        
    $fntype___c4ca4238a0b923820dcc509a6f75849b = $this->context->context->functionType(
                $this->context->getTypeFromString('int8*'),
                false , 
                $this->context->getTypeFromString('int8*')
                , $this->context->getTypeFromString('size_t')
                
            );
            $fn___c4ca4238a0b923820dcc509a6f75849b = $this->context->module->addFunction('_erealloc', $fntype___c4ca4238a0b923820dcc509a6f75849b);
            
            
            
            
            $this->context->registerFunction('_erealloc', $fn___c4ca4238a0b923820dcc509a6f75849b);
        

        

        
    $fntype___c4ca4238a0b923820dcc509a6f75849b = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('int8*')
                
            );
            $fn___c4ca4238a0b923820dcc509a6f75849b = $this->context->module->addFunction('_efree', $fntype___c4ca4238a0b923820dcc509a6f75849b);
            
            
            
            $this->context->registerFunction('_efree', $fn___c4ca4238a0b923820dcc509a6f75849b);
        

        

        
    
        }
    }

    public function implement(): void {
        if (\PHP_DEBUG) {
            // FIXME: Use real values here, not constants.

            // These variables are a hack because compile{}
            // blocks currently only accept variables as arguments.
            $jit = $this->context->constantFromString("jit");
            $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = 2;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $two = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $two = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $two = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $two = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $two = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $two = $__type->constReal(2);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $two = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $two = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $two = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $two = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $two = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    

            $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $this->context->lookupFunction('__mm__malloc');
    $block___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___eccbc87e4b5ce2fe28308fd9f2a7baf3);
    $size = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->getParam(0);
    
    $result = $this->context->builder->call(
                        $this->context->lookupFunction('_emalloc') , 
                        $size
                        , $jit
                        , $two
                        , $jit
                        , $two
                        
                    );
    $this->context->builder->returnValue($result);
    
    $this->context->builder->clearInsertionPosition();
            $fn___e4da3b7fbbce2345d7772b0674a318d5 = $this->context->lookupFunction('__mm__realloc');
    $block___e4da3b7fbbce2345d7772b0674a318d5 = $fn___e4da3b7fbbce2345d7772b0674a318d5->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___e4da3b7fbbce2345d7772b0674a318d5);
    $void = $fn___e4da3b7fbbce2345d7772b0674a318d5->getParam(0);
    $size = $fn___e4da3b7fbbce2345d7772b0674a318d5->getParam(1);
    
    $result = $this->context->builder->call(
                        $this->context->lookupFunction('_erealloc') , 
                        $void
                        , $size
                        , $jit
                        , $two
                        , $jit
                        , $two
                        
                    );
    $this->context->builder->returnValue($result);
    
    $this->context->builder->clearInsertionPosition();
            $fn___8f14e45fceea167a5a36dedd4bea2543 = $this->context->lookupFunction('__mm__free');
    $block___8f14e45fceea167a5a36dedd4bea2543 = $fn___8f14e45fceea167a5a36dedd4bea2543->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___8f14e45fceea167a5a36dedd4bea2543);
    $void = $fn___8f14e45fceea167a5a36dedd4bea2543->getParam(0);
    
    $this->context->builder->call(
                    $this->context->lookupFunction('_efree') , 
                    $void
                    , $jit
                    , $two
                    , $jit
                    , $two
                    
                );
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
        } else {
            $fn___45c48cce2e2d7fbdea1afc51c7c6ad26 = $this->context->lookupFunction('__mm__malloc');
    $block___45c48cce2e2d7fbdea1afc51c7c6ad26 = $fn___45c48cce2e2d7fbdea1afc51c7c6ad26->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___45c48cce2e2d7fbdea1afc51c7c6ad26);
    $size = $fn___45c48cce2e2d7fbdea1afc51c7c6ad26->getParam(0);
    
    $result = $this->context->builder->call(
                        $this->context->lookupFunction('_emalloc') , 
                        $size
                        
                    );
    $this->context->builder->returnValue($result);
    
    $this->context->builder->clearInsertionPosition();
            $fn___6512bd43d9caa6e02c990b0a82652dca = $this->context->lookupFunction('__mm__realloc');
    $block___6512bd43d9caa6e02c990b0a82652dca = $fn___6512bd43d9caa6e02c990b0a82652dca->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___6512bd43d9caa6e02c990b0a82652dca);
    $void = $fn___6512bd43d9caa6e02c990b0a82652dca->getParam(0);
    $size = $fn___6512bd43d9caa6e02c990b0a82652dca->getParam(1);
    
    $result = $this->context->builder->call(
                        $this->context->lookupFunction('_erealloc') , 
                        $void
                        , $size
                        
                    );
    $this->context->builder->returnValue($result);
    
    $this->context->builder->clearInsertionPosition();
            $fn___c51ce410c124a10e0db5e4b97fc2af39 = $this->context->lookupFunction('__mm__free');
    $block___c51ce410c124a10e0db5e4b97fc2af39 = $fn___c51ce410c124a10e0db5e4b97fc2af39->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___c51ce410c124a10e0db5e4b97fc2af39);
    $void = $fn___c51ce410c124a10e0db5e4b97fc2af39->getParam(0);
    
    $this->context->builder->call(
                    $this->context->lookupFunction('_efree') , 
                    $void
                    
                );
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
        }
    }
}
