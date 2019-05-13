<?php

// This file is generated and changes you make will be lost.
// Change /compiler/lib/Builtin/Refcount.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;
use PHPLLVM;

class Refcount extends Builtin {
    const TYPE_INFO_NONREFCOUNTED     = 0b0000000000;
    const TYPE_INFO_REFCOUNTED        = 0b0000000001;
    const TYPE_INFO_NONREFCOUNTED_MASK = ~self::TYPE_INFO_REFCOUNTED;

    const TYPE_INFO_TYPEMASK          = 0b1111111100;
    const TYPE_INFO_TYPE_STRING       = 0b0000000100;
    const TYPE_INFO_TYPE_OBJECT       = 0b0000001000;
    const TYPE_INFO_TYPE_MASKED_ARRAY = 0b0000001100;
    const TYPE_INFO_TYPE_VALUE        = 0b0000010000;

    public PHPLLVM\Type $struct;
    public PHPLLVM\Type $virtualStruct;

    public PHPLLVM\Type $pointer;
    public PHPLLVM\Type $doublePointer;
    
    private array $fields;

    public function register(): void {
        

        

        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__ref__');
            // declare first so recursive structs are possible :)
            $this->context->registerType('__ref__', $struct___cfcd208495d565ef66e7dff9f98764da);
            $this->context->registerType('__ref__' . '*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
            $this->context->registerType('__ref__' . '**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
            $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false ,  // packed
                $this->context->getTypeFromString('int32')
                , $this->context->getTypeFromString('int32')
                
            );
            $this->context->structFieldMap['__ref__'] = [
                'refcount' => 0
                , 'typeinfo' => 1
                
            ];
        
    

        

        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__ref__virtual');
            // declare first so recursive structs are possible :)
            $this->context->registerType('__ref__virtual', $struct___cfcd208495d565ef66e7dff9f98764da);
            $this->context->registerType('__ref__virtual' . '*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
            $this->context->registerType('__ref__virtual' . '**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
            $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false ,  // packed
                $this->context->getTypeFromString('__ref__')
                
            );
            $this->context->structFieldMap['__ref__virtual'] = [
                'ref' => 0
                
            ];
        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('int32')
                , $this->context->getTypeFromString('__ref__virtual*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__init', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            
            $this->context->registerFunction('__ref__init', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__ref__virtual*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__addref', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__ref__addref', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__ref__virtual*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__delref', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__ref__delref', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__ref__virtual**')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__separate', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__ref__separate', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__ref__virtual**')
                , $this->context->getTypeFromString('int32')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__separate_ex', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(\PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            
            $this->context->registerFunction('__ref__separate_ex', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    
        $this->struct = $this->context->getTypeFromString('__ref__');
        $this->virtualStruct = $this->context->getTypeFromString('__ref__virtual');
        $this->pointer = $this->context->getTypeFromString('__ref__virtual*');
        $this->doublePointer = $this->context->getTypeFromString('__ref__virtual**');
    }

    public function implement(): void {
        $this->implementInit();
        $this->implementAddref();
        $this->implementDelref();
        $this->implementSeparate();
    }

    private function implementInit(): void {
        $fn___c4ca4238a0b923820dcc509a6f75849b = $this->context->lookupFunction('__ref__init');
    $block___c4ca4238a0b923820dcc509a6f75849b = $fn___c4ca4238a0b923820dcc509a6f75849b->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___c4ca4238a0b923820dcc509a6f75849b);
    $typeinfo = $fn___c4ca4238a0b923820dcc509a6f75849b->getParam(0);
    $refVirtual = $fn___c4ca4238a0b923820dcc509a6f75849b->getParam(1);
    
    $offset = $this->context->structFieldMap[$refVirtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($refVirtual, $offset)
                    );
    $structType = $ref->typeOf();
                $offset = $this->context->structFieldMap[$structType->getName()]['refcount'];

                $this->context->builder->insertValue(
                    $ref, 
                    $structType->getElementAtIndex($offset)->constInt(0, false),
                    $offset
                );
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                $this->context->builder->insertValue(
                    $ref, 
                    $typeinfo,
                    $offset
                );
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
    } 

    private function implementAddref(): void {
        $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $this->context->lookupFunction('__ref__addref');
    $block___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___eccbc87e4b5ce2fe28308fd9f2a7baf3);
    $refVirtual = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->getParam(0);
    
    $isNull = $this->context->builder->icmp(\PHPLLVM\Builder::INT_EQ, $refVirtual, $refVirtual->typeOf()->constNull());
    $bool = $this->context->castToBool($isNull);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $this->context->builder->returnVoid();
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $offset = $this->context->structFieldMap[$refVirtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($refVirtual, $offset)
                    );
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset);
    $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = self::TYPE_INFO_REFCOUNTED;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $refMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $refMask = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $refMask = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $refMask = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $refMask = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $refMask = $__type->constReal(self::TYPE_INFO_REFCOUNTED);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $refMask = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $refMask = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $refMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $refMask = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $refMask = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $__right = $this->context->builder->intCast($refMask, $typeinfo->typeOf());
                            
                            
                        

                        $isCounted = $this->context->builder->bitwiseAnd($typeinfo, $__right);
    $bool = $this->context->castToBool($isCounted);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                    $current = $this->context->builder->extractValue($ref, $offset);
    $current = $this->context->builder->add($current, $current->typeOf()->constInt(1, false));
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                $this->context->builder->insertValue(
                    $ref, 
                    $current,
                    $offset
                );
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
    }

    private function implementDelref(): void {
        $fn___8f14e45fceea167a5a36dedd4bea2543 = $this->context->lookupFunction('__ref__delref');
    $block___8f14e45fceea167a5a36dedd4bea2543 = $fn___8f14e45fceea167a5a36dedd4bea2543->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___8f14e45fceea167a5a36dedd4bea2543);
    $refVirtual = $fn___8f14e45fceea167a5a36dedd4bea2543->getParam(0);
    
    $isNull = $this->context->builder->icmp(\PHPLLVM\Builder::INT_EQ, $refVirtual, $refVirtual->typeOf()->constNull());
    $bool = $this->context->castToBool($isNull);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $this->context->builder->returnVoid();
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $offset = $this->context->structFieldMap[$refVirtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($refVirtual, $offset)
                    );
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset);
    $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = self::TYPE_INFO_REFCOUNTED;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $refMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $refMask = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $refMask = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $refMask = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $refMask = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $refMask = $__type->constReal(self::TYPE_INFO_REFCOUNTED);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $refMask = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $refMask = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $refMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $refMask = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $refMask = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $__right = $this->context->builder->intCast($refMask, $typeinfo->typeOf());
                            
                            
                        

                        $isCounted = $this->context->builder->bitwiseAnd($typeinfo, $__right);
    $bool = $this->context->castToBool($isCounted);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                    $current = $this->context->builder->extractValue($ref, $offset);
    $current = $this->context->builder->sub($current, $current->typeOf()->constInt(1, false));
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                $this->context->builder->insertValue(
                    $ref, 
                    $current,
                    $offset
                );
    $__right = $current->typeOf()->constInt(0, false);
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $cmp = \PHPLLVM\Builder::INT_SLE;
                            
                            $test = $this->context->builder->icmp($cmp, $current, $__right);
    $bool = $this->context->castToBool($test);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $this->context->memory->free($refVirtual);
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
    }

    private function implementSeparate(): void {
        // TODO
        $fn___c20ad4d76fe97759aa27a0c99bff6710 = $this->context->lookupFunction('__ref__separate');
    $block___c20ad4d76fe97759aa27a0c99bff6710 = $fn___c20ad4d76fe97759aa27a0c99bff6710->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___c20ad4d76fe97759aa27a0c99bff6710);
    $virtualPtr = $fn___c20ad4d76fe97759aa27a0c99bff6710->getParam(0);
    
    $virtual = $this->context->builder->load($virtualPtr);
    $test = $this->context->builder->icmp(\PHPLLVM\Builder::INT_EQ, $virtual, $virtual->typeOf()->constNull());
    $bool = $this->context->castToBool($test);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $this->context->builder->returnVoid();
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $offset = $this->context->structFieldMap[$virtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($virtual, $offset)
                    );
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset);
    $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = self::TYPE_INFO_REFCOUNTED;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $refMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $refMask = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $refMask = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $refMask = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $refMask = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $refMask = $__type->constReal(self::TYPE_INFO_REFCOUNTED);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $refMask = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $refMask = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $refMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $refMask = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $refMask = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $__right = $this->context->builder->intCast($refMask, $typeinfo->typeOf());
                            
                            
                        

                        $isCounted = $this->context->builder->bitwiseAnd($typeinfo, $__right);
    $bool = $this->context->castToBool($isCounted);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                    $current = $this->context->builder->extractValue($ref, $offset);
    $__right = $current->typeOf()->constInt(1, false);
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $cmp = \PHPLLVM\Builder::INT_SGT;
                            
                            $test = $this->context->builder->icmp($cmp, $current, $__right);
    $bool = $this->context->castToBool($test);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $this->context->builder->call(
                    $this->context->lookupFunction('__ref__separate_ex') , 
                    $virtualPtr
                    , $typeinfo
                    
                );
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();

        $fn___70efdf2ec9b086079795c442636b55fb = $this->context->lookupFunction('__ref__separate_ex');
    $block___70efdf2ec9b086079795c442636b55fb = $fn___70efdf2ec9b086079795c442636b55fb->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___70efdf2ec9b086079795c442636b55fb);
    $virtualPtr = $fn___70efdf2ec9b086079795c442636b55fb->getParam(0);
    $typeinfo = $fn___70efdf2ec9b086079795c442636b55fb->getParam(1);
    
    $virtual = $this->context->builder->load($virtualPtr);
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__delref') , 
                    $virtual
                    
                );
    $offset = $this->context->structFieldMap[$virtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($virtual, $offset)
                    );
    $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = self::TYPE_INFO_TYPEMASK;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $typeMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $typeMask = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $typeMask = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $typeMask = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $typeMask = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $typeMask = $__type->constReal(self::TYPE_INFO_TYPEMASK);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $typeMask = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $typeMask = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $typeMask = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $typeMask = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $typeMask = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $__right = $this->context->builder->intCast($typeMask, $typeinfo->typeOf());
                            
                            
                        

                        $type = $this->context->builder->bitwiseAnd($typeinfo, $__right);
    $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = self::TYPE_INFO_TYPE_STRING;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $stringType = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $stringType = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $stringType = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $stringType = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $stringType = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $stringType = $__type->constReal(self::TYPE_INFO_TYPE_STRING);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $stringType = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $stringType = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $stringType = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $stringType = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $stringType = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $__right = $this->context->builder->intCast($stringType, $type->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        $isString = $this->context->builder->icmp(\PHPLLVM\Builder::INT_EQ, $type, $__right);
    $bool = $this->context->castToBool($isString);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $__type = $this->context->getTypeFromString('__string__*');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $virtual;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $string = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $string = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $string = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $string = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $string = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $string = $__type->constReal($virtual);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $string = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $string = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $string = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $string = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $string = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $stringResult = $this->context->builder->call(
                        $this->context->lookupFunction('__string__separate') , 
                        $string
                        
                    );
    $__type = $this->context->getTypeFromString('__ref__virtual*');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $stringResult;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $result = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $result = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $result = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $result = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $result = $__type->constReal($stringResult);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $result = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $result = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $result = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $result = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $result = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $this->context->builder->store($result, $virtualPtr);
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
    }

    public function disableRefcount(PHPLLVM\Value $value): void {
        $__type = $this->context->getTypeFromString('__ref__virtual*');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $value;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $virtual = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $virtual = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $virtual = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $virtual = $__type->constReal($value);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $virtual = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $virtual = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $virtual = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $offset = $this->context->structFieldMap[$virtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($virtual, $offset)
                    );
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset);
    $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = self::TYPE_INFO_NONREFCOUNTED_MASK;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $notRefc = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $notRefc = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $notRefc = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $notRefc = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $notRefc = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $notRefc = $__type->constReal(self::TYPE_INFO_NONREFCOUNTED_MASK);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $notRefc = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $notRefc = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $notRefc = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $notRefc = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $notRefc = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $__right = $this->context->builder->intCast($notRefc, $typeinfo->typeOf());
                            
                            
                        

                        $typeinfo = $this->context->builder->bitwiseAnd($typeinfo, $__right);
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                $this->context->builder->insertValue(
                    $ref, 
                    $typeinfo,
                    $offset
                );
    
    }

    public function init(PHPLLVM\Value $value, int $typeinfo = 0): void {
        $__type = $this->context->getTypeFromString('int32');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $typeinfo;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $typeinfo = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $typeinfo = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $typeinfo = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $typeinfo = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $typeinfo = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $typeinfo = $__type->constReal($typeinfo);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $typeinfo = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $typeinfo = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $typeinfo = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $typeinfo = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $typeinfo = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__init') , 
                    $typeinfo
                    , $value
                    
                );
    
    }

    public function addref(PHPLLVM\Value $value): void {
        $__type = $this->context->getTypeFromString('__ref__virtual*');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $value;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $virtual = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $virtual = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $virtual = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $virtual = $__type->constReal($value);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $virtual = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $virtual = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $virtual = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__addref') , 
                    $virtual
                    
                );
    
    }

    public function delref(PHPLLVM\Value $value): void {
        $__type = $this->context->getTypeFromString('__ref__virtual*');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $value;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $virtual = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $virtual = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $virtual = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $virtual = $__type->constReal($value);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $virtual = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $virtual = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $virtual = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__delref') , 
                    $virtual
                    
                );
    
    }

    public function separate(PHPLLVM\Value $value): void {
        $__type = $this->context->getTypeFromString('__ref__virtual**');
                        
                    
                    $__kind = $__type->getKind();
                    $__value = $value;
                    switch ($__kind) {
                        case \PHPLLVM\Type::KIND_INTEGER:
                            if (!is_object($__value)) {
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    if ($__other_type->getWidth() >= $__type->getWidth()) {
                                        $virtual = $this->context->builder->truncOrBitCast($__value, $__type);
                                    } else {
                                        $virtual = $this->context->builder->zExtOrBitCast($__value, $__type);
                                    }
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    
                                    $virtual = $this->context->builder->fpToSi($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->ptrToInt($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (int, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_DOUBLE:
                            if (!is_object($__value)) {
                                $virtual = $__type->constReal($value);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    
                                    $virtual = $this->context->builder->siToFp($__value, $__type);
                                    
                                    break;
                                case \PHPLLVM\Type::KIND_DOUBLE:
                                    $virtual = $this->context->builder->fpCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        case \PHPLLVM\Type::KIND_ARRAY:
                        case \PHPLLVM\Type::KIND_POINTER:
                            if (!is_object($__value)) {
                                // this is very likely very wrong...
                                $virtual = $__type->constInt($__value, false);
                                break;
                            }
                            $__other_type = $__value->typeOf();
                            switch ($__other_type->getKind()) {
                                case \PHPLLVM\Type::KIND_INTEGER:
                                    $virtual = $this->context->builder->intToPtr($__value, $__type);
                                    break;
                                case \PHPLLVM\Type::KIND_ARRAY:
                                    // $__tmp = $this->context->builder->($__value, $this->context->context->int64Type());
                                    // $(result) = $this->context->builder->intToPtr($__tmp, $__type);
                                    // break;
                                case \PHPLLVM\Type::KIND_POINTER:
                                    $virtual = $this->context->builder->pointerCast($__value, $__type);
                                    break;
                                default:
                                    throw new \LogicException("Unknown how to handle type pair (double, " . $__other_type->toString() . ")");
                            }
                            break;
                        default:
                            throw new \LogicException("Unsupported type cast: " . $__type->toString());
                    }
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__separate') , 
                    $virtual
                    
                );
    
    }
}