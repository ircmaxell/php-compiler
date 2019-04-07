<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/lib/JIT/Builtin/Refcount.pre instead.

// First, expand statements
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

    public PHPLLVM\Type $struct;
    public PHPLLVM\Type $virtualStruct;

    public PHPLLVM\Type $pointer;
    public PHPLLVM\Type $doublePointer;
    
    private array $fields;

    public function register(): void {
        

        

        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__ref__');
            $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false ,  // packed
                $this->context->getTypeFromString('int32')
                , $this->context->getTypeFromString('int32')
                
            );
            $this->context->registerType('__ref__', $struct___cfcd208495d565ef66e7dff9f98764da);
            $this->context->registerType('__ref__' . '*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
            $this->context->registerType('__ref__' . '**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
            $this->context->structFieldMap['__ref__'] = [
                'refcount' => 0
                , 'typeinfo' => 1
                
            ];
        
    

        

        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__ref__virtual');
            $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false ,  // packed
                $this->context->getTypeFromString('__ref__')
                
            );
            $this->context->registerType('__ref__virtual', $struct___cfcd208495d565ef66e7dff9f98764da);
            $this->context->registerType('__ref__virtual' . '*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
            $this->context->registerType('__ref__virtual' . '**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
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
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            
            $this->context->registerFunction('__ref__init', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__ref__virtual*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__addref', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__ref__addref', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__ref__virtual*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__delref', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__ref__delref', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__ref__virtual**')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__ref__separate', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__ref__separate', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    
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
    
    $offset___c81e728d9d4c2f636f067f89cc14862c = $this->context->structFieldMap[$refVirtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($refVirtual, $offset___c81e728d9d4c2f636f067f89cc14862c)
                    );
    $structType___c81e728d9d4c2f636f067f89cc14862c = $ref->typeOf();
                $offset___c81e728d9d4c2f636f067f89cc14862c = $this->context->structFieldMap[$structType___c81e728d9d4c2f636f067f89cc14862c->getName()]['refcount'];

                $this->context->builder->insertValue(
                    $ref, 
                    $structType___c81e728d9d4c2f636f067f89cc14862c->getElementAtIndex($offset___c81e728d9d4c2f636f067f89cc14862c)->constInt(0, false),
                    $offset___c81e728d9d4c2f636f067f89cc14862c
                );
    $offset___c81e728d9d4c2f636f067f89cc14862c = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                $this->context->builder->insertValue(
                    $ref, 
                    $typeinfo,
                    $offset___c81e728d9d4c2f636f067f89cc14862c
                );
    $this->context->builder->returnVoid();
    
    } 

    private function implementAddref(): void {
        $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $this->context->lookupFunction('__ref__addref');
    $block___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___eccbc87e4b5ce2fe28308fd9f2a7baf3);
    $refVirtual = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->getParam(0);
    
    $isNull = $this->context->builder->icmp(PHPLLVM\Builder::INT_EQ, $refVirtual, $refVirtual->typeOf()->constNull());
    $bool___a87ff679a2f3e71d9181a67b7542122c = $this->context->castToBool($isNull);
                $prev___a87ff679a2f3e71d9181a67b7542122c = $this->context->builder->getInsertBlock();
                $ifBlock___a87ff679a2f3e71d9181a67b7542122c = $prev___a87ff679a2f3e71d9181a67b7542122c->insertBasicBlock('ifBlock');
                $prev___a87ff679a2f3e71d9181a67b7542122c->moveBefore($ifBlock___a87ff679a2f3e71d9181a67b7542122c);
                
                $endBlock___a87ff679a2f3e71d9181a67b7542122c[] = $tmp___a87ff679a2f3e71d9181a67b7542122c = $ifBlock___a87ff679a2f3e71d9181a67b7542122c->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool___a87ff679a2f3e71d9181a67b7542122c, $ifBlock___a87ff679a2f3e71d9181a67b7542122c, $tmp___a87ff679a2f3e71d9181a67b7542122c);
                
                $this->context->builder->positionAtEnd($ifBlock___a87ff679a2f3e71d9181a67b7542122c);
                { $this->context->builder->returnVoid();
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock___a87ff679a2f3e71d9181a67b7542122c));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock___a87ff679a2f3e71d9181a67b7542122c));
    $offset___a87ff679a2f3e71d9181a67b7542122c = $this->context->structFieldMap[$refVirtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($refVirtual, $offset___a87ff679a2f3e71d9181a67b7542122c)
                    );
    $offset___a87ff679a2f3e71d9181a67b7542122c = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset___a87ff679a2f3e71d9181a67b7542122c);
    $refMask = $this->context->getTypeFromString('int32')->constInt(self::TYPE_INFO_REFCOUNTED, false);
    $isCounted = $this->context->builder->bitwiseAnd($typeinfo, $refMask);
    $bool___a87ff679a2f3e71d9181a67b7542122c = $this->context->castToBool($isCounted);
                $prev___a87ff679a2f3e71d9181a67b7542122c = $this->context->builder->getInsertBlock();
                $ifBlock___a87ff679a2f3e71d9181a67b7542122c = $prev___a87ff679a2f3e71d9181a67b7542122c->insertBasicBlock('ifBlock');
                $prev___a87ff679a2f3e71d9181a67b7542122c->moveBefore($ifBlock___a87ff679a2f3e71d9181a67b7542122c);
                
                $endBlock___a87ff679a2f3e71d9181a67b7542122c[] = $tmp___a87ff679a2f3e71d9181a67b7542122c = $ifBlock___a87ff679a2f3e71d9181a67b7542122c->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool___a87ff679a2f3e71d9181a67b7542122c, $ifBlock___a87ff679a2f3e71d9181a67b7542122c, $tmp___a87ff679a2f3e71d9181a67b7542122c);
                
                $this->context->builder->positionAtEnd($ifBlock___a87ff679a2f3e71d9181a67b7542122c);
                { $offset___a87ff679a2f3e71d9181a67b7542122c = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                    $current = $this->context->builder->extractValue($ref, $offset___a87ff679a2f3e71d9181a67b7542122c);
    $current = $this->context->builder->add($current, $current->typeOf()->constInt(1, false));
    $offset___a87ff679a2f3e71d9181a67b7542122c = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                $this->context->builder->insertValue(
                    $ref, 
                    $current,
                    $offset___a87ff679a2f3e71d9181a67b7542122c
                );
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock___a87ff679a2f3e71d9181a67b7542122c));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock___a87ff679a2f3e71d9181a67b7542122c));
    $this->context->builder->returnVoid();
    
    }

    private function implementDelref(): void {
        $fn___8f14e45fceea167a5a36dedd4bea2543 = $this->context->lookupFunction('__ref__delref');
    $block___8f14e45fceea167a5a36dedd4bea2543 = $fn___8f14e45fceea167a5a36dedd4bea2543->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___8f14e45fceea167a5a36dedd4bea2543);
    $refVirtual = $fn___8f14e45fceea167a5a36dedd4bea2543->getParam(0);
    
    $isNull = $this->context->builder->icmp(PHPLLVM\Builder::INT_EQ, $refVirtual, $refVirtual->typeOf()->constNull());
    $bool___c9f0f895fb98ab9159f51fd0297e236d = $this->context->castToBool($isNull);
                $prev___c9f0f895fb98ab9159f51fd0297e236d = $this->context->builder->getInsertBlock();
                $ifBlock___c9f0f895fb98ab9159f51fd0297e236d = $prev___c9f0f895fb98ab9159f51fd0297e236d->insertBasicBlock('ifBlock');
                $prev___c9f0f895fb98ab9159f51fd0297e236d->moveBefore($ifBlock___c9f0f895fb98ab9159f51fd0297e236d);
                
                $endBlock___c9f0f895fb98ab9159f51fd0297e236d[] = $tmp___c9f0f895fb98ab9159f51fd0297e236d = $ifBlock___c9f0f895fb98ab9159f51fd0297e236d->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool___c9f0f895fb98ab9159f51fd0297e236d, $ifBlock___c9f0f895fb98ab9159f51fd0297e236d, $tmp___c9f0f895fb98ab9159f51fd0297e236d);
                
                $this->context->builder->positionAtEnd($ifBlock___c9f0f895fb98ab9159f51fd0297e236d);
                { $this->context->builder->returnVoid();
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock___c9f0f895fb98ab9159f51fd0297e236d));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock___c9f0f895fb98ab9159f51fd0297e236d));
    $offset___c9f0f895fb98ab9159f51fd0297e236d = $this->context->structFieldMap[$refVirtual->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($refVirtual, $offset___c9f0f895fb98ab9159f51fd0297e236d)
                    );
    $offset___c9f0f895fb98ab9159f51fd0297e236d = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset___c9f0f895fb98ab9159f51fd0297e236d);
    $refMask = $this->context->getTypeFromString('int32')->constInt(self::TYPE_INFO_REFCOUNTED, false);
    $isCounted = $this->context->builder->bitwiseAnd($typeinfo, $refMask);
    $bool___c9f0f895fb98ab9159f51fd0297e236d = $this->context->castToBool($isCounted);
                $prev___c9f0f895fb98ab9159f51fd0297e236d = $this->context->builder->getInsertBlock();
                $ifBlock___c9f0f895fb98ab9159f51fd0297e236d = $prev___c9f0f895fb98ab9159f51fd0297e236d->insertBasicBlock('ifBlock');
                $prev___c9f0f895fb98ab9159f51fd0297e236d->moveBefore($ifBlock___c9f0f895fb98ab9159f51fd0297e236d);
                
                $endBlock___c9f0f895fb98ab9159f51fd0297e236d[] = $tmp___c9f0f895fb98ab9159f51fd0297e236d = $ifBlock___c9f0f895fb98ab9159f51fd0297e236d->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool___c9f0f895fb98ab9159f51fd0297e236d, $ifBlock___c9f0f895fb98ab9159f51fd0297e236d, $tmp___c9f0f895fb98ab9159f51fd0297e236d);
                
                $this->context->builder->positionAtEnd($ifBlock___c9f0f895fb98ab9159f51fd0297e236d);
                { $offset___c9f0f895fb98ab9159f51fd0297e236d = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                    $current = $this->context->builder->extractValue($ref, $offset___c9f0f895fb98ab9159f51fd0297e236d);
    $current = $this->context->builder->sub($current, $current->typeOf()->constInt(1, false));
    $offset___c9f0f895fb98ab9159f51fd0297e236d = $this->context->structFieldMap[$ref->typeOf()->getName()]['refcount'];
                $this->context->builder->insertValue(
                    $ref, 
                    $current,
                    $offset___c9f0f895fb98ab9159f51fd0297e236d
                );
    $test = $this->context->builder->icmp(
                        PHPLLVM\Builder::INT_SLE,
                        $current,
                        $current->typeOf()->constInt(0, false)
                    );
    $bool___c9f0f895fb98ab9159f51fd0297e236d = $this->context->castToBool($test);
                $prev___c9f0f895fb98ab9159f51fd0297e236d = $this->context->builder->getInsertBlock();
                $ifBlock___c9f0f895fb98ab9159f51fd0297e236d = $prev___c9f0f895fb98ab9159f51fd0297e236d->insertBasicBlock('ifBlock');
                $prev___c9f0f895fb98ab9159f51fd0297e236d->moveBefore($ifBlock___c9f0f895fb98ab9159f51fd0297e236d);
                
                $endBlock___c9f0f895fb98ab9159f51fd0297e236d[] = $tmp___c9f0f895fb98ab9159f51fd0297e236d = $ifBlock___c9f0f895fb98ab9159f51fd0297e236d->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool___c9f0f895fb98ab9159f51fd0297e236d, $ifBlock___c9f0f895fb98ab9159f51fd0297e236d, $tmp___c9f0f895fb98ab9159f51fd0297e236d);
                
                $this->context->builder->positionAtEnd($ifBlock___c9f0f895fb98ab9159f51fd0297e236d);
                { $this->context->memory->free($refVirtual);
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock___c9f0f895fb98ab9159f51fd0297e236d));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock___c9f0f895fb98ab9159f51fd0297e236d));
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock___c9f0f895fb98ab9159f51fd0297e236d));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock___c9f0f895fb98ab9159f51fd0297e236d));
    $this->context->builder->returnVoid();
    
    }

    private function implementSeparate(): void {
        $func = $this->context->lookupFunction('__ref__separate');
        $func->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
        // TODO
    }

    public function disableRefcount(PHPLLVM\Value $value): void {
        $virtual = $this->context->builder->pointerCast(
                        $value, 
                        $this->context->getTypeFromString('__ref__virtual*')
                    );
    $offset___c20ad4d76fe97759aa27a0c99bff6710 = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset___c20ad4d76fe97759aa27a0c99bff6710)
                    );
    $offset___c20ad4d76fe97759aa27a0c99bff6710 = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset___c20ad4d76fe97759aa27a0c99bff6710);
    $notRefc = $this->context->getTypeFromString('int32')->constInt(self::TYPE_INFO_NONREFCOUNTED_MASK, false);
    $typeinfo = $this->context->builder->bitwiseAnd($typeinfo, $notRefc);
    $offset___c20ad4d76fe97759aa27a0c99bff6710 = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                $this->context->builder->insertValue(
                    $ref, 
                    $typeinfo,
                    $offset___c20ad4d76fe97759aa27a0c99bff6710
                );
    
    }

    public function init(PHPLLVM\Value $value, int $typeinfo = 0): void {
        $type___c51ce410c124a10e0db5e4b97fc2af39 = $this->context->getTypeFromString('int32');
                    if (!is_object($typeinfo)) {
                        $typeinfo = $type___c51ce410c124a10e0db5e4b97fc2af39->constInt($typeinfo, false);
                    } elseif ($typeinfo->typeOf()->getWidth() >= $type___c51ce410c124a10e0db5e4b97fc2af39->getWidth()) {
                        $typeinfo = $this->context->builder->truncOrBitCast($typeinfo, $type___c51ce410c124a10e0db5e4b97fc2af39);
                    } else {
                        $typeinfo = $this->context->builder->zExtOrBitCast($typeinfo, $type___c51ce410c124a10e0db5e4b97fc2af39);
                    }
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__init') , 
                    $typeinfo
                    , $value
                    
                );
    
    }

    public function addref(PHPLLVM\Value $value): void {
        $value = $this->context->builder->bitcast($value, $this->pointer);
        $this->context->builder->call(
                    $this->context->lookupFunction('__ref__addref') , 
                    $value
                    
                );
    
    }

    public function delref(PHPLLVM\Value $value): void {
        $value = $this->context->builder->bitcast($value, $this->pointer);
        $this->context->builder->call(
                    $this->context->lookupFunction('__ref__delref') , 
                    $value
                    
                );
    
    }

    public function separate(PHPLLVM\Value $value): void {
        $value = $this->context->builder->bitcast($value, $this->doublePointer);
        $this->context->builder->call(
                    $this->context->lookupFunction('__ref__separate') , 
                    $value
                    
                );
    
    }
}