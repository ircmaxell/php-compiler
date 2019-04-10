<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/lib/JIT/Builtin/Type/String_.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin\Type;

use PHPCompiler\JIT\Builtin\Type;
use PHPCompiler\JIT\Builtin\Refcount;
use PHPCompiler\JIT\Variable;

use PHPLLVM;

class String_ extends Type {
    public PHPLLVM\Type $pointer;

    public function register(): void {
        

        

        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__string__');
            // declare first so recursive structs are possible :)
            $this->context->registerType('__string__', $struct___cfcd208495d565ef66e7dff9f98764da);
            $this->context->registerType('__string__' . '*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
            $this->context->registerType('__string__' . '**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
            $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false ,  // packed
                $this->context->getTypeFromString('__ref__')
                , $this->context->getTypeFromString('int64')
                , $this->context->getTypeFromString('int8')
                
            );
            $this->context->structFieldMap['__string__'] = [
                'ref' => 0
                , 'length' => 1
                , 'value' => 2
                
            ];
        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('int64'),
                false , 
                $this->context->getTypeFromString('__string__*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__string__strlen', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__string__strlen', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('__string__*'),
                false , 
                $this->context->getTypeFromString('int64')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__string__alloc', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__string__alloc', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('void'),
                false , 
                $this->context->getTypeFromString('__string__**')
                , $this->context->getTypeFromString('int64')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__string__realloc', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            
            $this->context->registerFunction('__string__realloc', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('__string__*'),
                false , 
                $this->context->getTypeFromString('int64')
                , $this->context->getTypeFromString('char*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__string__init', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(1 + 1, $this->context->attributes['readonly'], 0);
                    $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(1 + 1, $this->context->attributes['nocapture'], 0);
                
            
            $this->context->registerFunction('__string__init', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    $fntype___cfcd208495d565ef66e7dff9f98764da = $this->context->context->functionType(
                $this->context->getTypeFromString('__string__*'),
                false , 
                $this->context->getTypeFromString('__string__*')
                
            );
            $fn___cfcd208495d565ef66e7dff9f98764da = $this->context->module->addFunction('__string__separate', $fntype___cfcd208495d565ef66e7dff9f98764da);
            $fn___cfcd208495d565ef66e7dff9f98764da->addAttributeAtIndex(PHPLLVM\Attribute::INDEX_FUNCTION, $this->context->attributes['alwaysinline']);
            
            
            
            $this->context->registerFunction('__string__separate', $fn___cfcd208495d565ef66e7dff9f98764da);
        

        

        
    
        $this->pointer = $this->context->getTypeFromString('__string__*');
    }

    public function implement(): void {

        $this->implementAlloc();
        $this->implementInit();
        $this->implementRealloc();
        $this->implementSeparate();
        $this->implementStrlen();
    }

    private function implementStrlen(): void {
        $fn___c4ca4238a0b923820dcc509a6f75849b = $this->context->lookupFunction('__string__strlen');
    $block___c4ca4238a0b923820dcc509a6f75849b = $fn___c4ca4238a0b923820dcc509a6f75849b->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___c4ca4238a0b923820dcc509a6f75849b);
    $string = $fn___c4ca4238a0b923820dcc509a6f75849b->getParam(0);
    
    $offset = $this->context->structFieldMap[$string->typeOf()->getElementType()->getName()]['length'];
                    $size = $this->context->builder->load(
                        $this->context->builder->structGep($string, $offset)
                    );
    $this->context->builder->returnValue($size);
    
    $this->context->builder->clearInsertionPosition();
    }

    private function implementAlloc(): void {
        $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $this->context->lookupFunction('__string__alloc');
    $block___eccbc87e4b5ce2fe28308fd9f2a7baf3 = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___eccbc87e4b5ce2fe28308fd9f2a7baf3);
    $size = $fn___eccbc87e4b5ce2fe28308fd9f2a7baf3->getParam(0);
    
    $__right = $size->typeOf()->constInt(1, false);
                            
                        

                        

                        

                        

                        
                            $allocSize = $this->context->builder->addNoSignedWrap($size, $__right);
    $type = $this->context->getTypeFromString('__string__');
                    $struct = $this->context->memory->mallocWithExtra($type, $size);
    $offset = $this->context->structFieldMap[$struct->typeOf()->getElementType()->getName()]['length'];
                $this->context->builder->store(
                    $size,
                    $this->context->builder->structGep($struct, $offset)
                );
    $offset = $this->context->structFieldMap[$struct->typeOf()->getElementType()->getName()]['value'];
                    $char = $this->context->builder->structGep($struct, $offset);
    $this->context->intrinsic->memset(
                    $char, 
                    $this->context->context->int8Type()->constInt(0, false),
                    $allocSize, 
                    false
                );
    $ref = $this->context->builder->pointerCast(
                        $struct, 
                        $this->context->getTypeFromString('__ref__virtual*')
                    );
    $typeinfo = $this->context->getTypeFromString('int32')->constInt(Refcount::TYPE_INFO_TYPE_STRING|Refcount::TYPE_INFO_REFCOUNTED, false);
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__init') , 
                    $typeinfo
                    , $ref
                    
                );
    $this->context->builder->returnValue($struct);
    
    $this->context->builder->clearInsertionPosition();
    }

    private function implementInit(): void {
        $fn___e4da3b7fbbce2345d7772b0674a318d5 = $this->context->lookupFunction('__string__init');
    $block___e4da3b7fbbce2345d7772b0674a318d5 = $fn___e4da3b7fbbce2345d7772b0674a318d5->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___e4da3b7fbbce2345d7772b0674a318d5);
    $size = $fn___e4da3b7fbbce2345d7772b0674a318d5->getParam(0);
    $value = $fn___e4da3b7fbbce2345d7772b0674a318d5->getParam(1);
    
    $result = $this->context->builder->call(
                        $this->context->lookupFunction('__string__alloc') , 
                        $size
                        
                    );
    $offset = $this->context->structFieldMap[$result->typeOf()->getElementType()->getName()]['value'];
                    $char = $this->context->builder->structGep($result, $offset);
    $this->context->intrinsic->memcpy($char, $value, $size, false);
    $this->context->builder->returnValue($result);
    
    $this->context->builder->clearInsertionPosition();
    }

    private function implementRealloc(): void {
        $fn___8f14e45fceea167a5a36dedd4bea2543 = $this->context->lookupFunction('__string__realloc');
    $block___8f14e45fceea167a5a36dedd4bea2543 = $fn___8f14e45fceea167a5a36dedd4bea2543->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___8f14e45fceea167a5a36dedd4bea2543);
    $doublePtr = $fn___8f14e45fceea167a5a36dedd4bea2543->getParam(0);
    $newSize = $fn___8f14e45fceea167a5a36dedd4bea2543->getParam(1);
    
    $refVirtual = $this->context->builder->pointerCast(
                        $doublePtr, 
                        $this->context->getTypeFromString('__ref__virtual**')
                    );
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__separate') , 
                    $refVirtual
                    
                );
    $destVar = $this->context->builder->load($doublePtr);
    $test = $this->context->builder->icmp(PHPLLVM\Builder::INT_EQ, $destVar, $destVar->typeOf()->constNull());
    $bool = $this->context->castToBool($test);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $result = $this->context->builder->call(
                        $this->context->lookupFunction('__string__alloc') , 
                        $newSize
                        
                    );
    $this->context->builder->store($result, $doublePtr);
    $this->context->builder->returnVoid();
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $offset = $this->context->structFieldMap[$destVar->typeOf()->getElementType()->getName()]['length'];
                    $oldSize = $this->context->builder->load(
                        $this->context->builder->structGep($destVar, $offset)
                    );
    $destValue = $this->context->memory->realloc($destVar, $newSize);
    $offset = $this->context->structFieldMap[$destValue->typeOf()->getElementType()->getName()]['length'];
                $this->context->builder->store(
                    $newSize,
                    $this->context->builder->structGep($destValue, $offset)
                );
    $offset = $this->context->structFieldMap[$destValue->typeOf()->getElementType()->getName()]['value'];
                    $char = $this->context->builder->structGep($destValue, $offset);
    $__right = $this->context->builder->intCast($oldSize, $newSize->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $cmp = PHPLLVM\Builder::INT_SGT;
                            
                            $test = $this->context->builder->icmp($cmp, $newSize, $__right);
    $bool = $this->context->castToBool($test);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $char = $this->context->builder->gep(
                        $char,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $oldSize
                    );
    $__right = $this->context->builder->intCast($oldSize, $newSize->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        
                            $diff = $this->context->builder->subNoSignedWrap($newSize, $__right);
    $this->context->intrinsic->memset(
                    $char, 
                    $this->context->context->int8Type()->constInt(0, false),
                    $diff, 
                    false
                );
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
    }

    private function implementSeparate(): void {
        $fn___6512bd43d9caa6e02c990b0a82652dca = $this->context->lookupFunction('__string__separate');
    $block___6512bd43d9caa6e02c990b0a82652dca = $fn___6512bd43d9caa6e02c990b0a82652dca->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___6512bd43d9caa6e02c990b0a82652dca);
    $string = $fn___6512bd43d9caa6e02c990b0a82652dca->getParam(0);
    
    $offset = $this->context->structFieldMap[$string->typeOf()->getElementType()->getName()]['length'];
                    $size = $this->context->builder->load(
                        $this->context->builder->structGep($string, $offset)
                    );
    $new = $this->context->builder->call(
                        $this->context->lookupFunction('__string__alloc') , 
                        $size
                        
                    );
    $offset = $this->context->structFieldMap[$string->typeOf()->getElementType()->getName()]['value'];
                    $char = $this->context->builder->structGep($string, $offset);
    $offset = $this->context->structFieldMap[$new->typeOf()->getElementType()->getName()]['value'];
                    $dest = $this->context->builder->structGep($new, $offset);
    $__right = $size->typeOf()->constInt(1, false);
                            
                        

                        

                        

                        

                        
                            $copySize = $this->context->builder->addNoSignedWrap($size, $__right);
    $this->context->intrinsic->memcpy($dest, $char, $copySize, false);
    $this->context->builder->returnValue($new);
    
    $this->context->builder->clearInsertionPosition();
    }

    public function initialize(): void {
    }

    private static $constId = 0;
    public function allocate(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $dest, 
        \gcc_jit_rvalue_ptr $length,
        bool $isConstant = false
    ): void {
        die("Not implemented");
    }

    public function init(
        PHPLLVM\Value $dest,
        PHPLLVM\Value $value,
        PHPLLVM\Value $length,
        bool $isConstant = false
    ): void {
        $value = $this->context->builder->pointerCast(
                        $value, 
                        $this->context->getTypeFromString('char*')
                    );
    $ptr = $this->context->builder->call(
                        $this->context->lookupFunction('__string__init') , 
                        $length
                        , $value
                        
                    );
    $this->context->builder->store($ptr, $dest);
    
        if ($isConstant) {
            // disable refcount
            $this->context->refcount->disableRefcount($ptr);
        }
    }

    public function reallocate(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $original, 
        \gcc_jit_rvalue_ptr $length
    ): void {
        $this->context->helper->assign(
            $block,
            $original,
            $this->context->helper->call(
                '__string__realloc',
                $original->asRValue(),
                $length
            )
        );
    }

    public function isString(PHPLLVM\Value $value): bool {
        throw new \LogicException("Unknown if it's a string due to type comparisons...");
    }

    public function size(Variable $var): \gcc_jit_rvalue_ptr {
        switch ($var->type) {
            case Variable::TYPE_STRING:

                // pointer call
                return $this->sizePtr($var->rvalue)->asRValue();
            case Variable::TYPE_NATIVE_LONG:
                return $this->context->helper->cast(
                    $this->context->helper->call(
                        'snprintf',
                        \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString('char*')),
                        $this->context->constantFromInteger(0, 'size_t'),
                        $this->context->constantFromString('%lld'),
                        $var->rvalue
                    ),
                    'size_t'
                );
        }
    }

    public function value(Variable $var): \gcc_jit_rvalue_ptr {
        switch ($var->type) {
            case Variable::TYPE_STRING:
                // pointer call
                return $this->valuePtr($var->rvalue);
        }
    }

    public function concat(Variable $dest, Variable $left, Variable $right): void {
        assert($dest->type === Variable::TYPE_STRING);
        if ($dest->kind === Variable::KIND_VALUE) {
            // What do do?
            throw new \LogicException("Unknown how to assign to a value");
        } else {
            $destVar = $dest->value;
        }
        $leftVar = $this->context->helper->loadValue($left);
        $rightVar = $this->context->helper->loadValue($right);

        $offset = $this->context->structFieldMap[$leftVar->typeOf()->getElementType()->getName()]['length'];
                    $leftSize = $this->context->builder->load(
                        $this->context->builder->structGep($leftVar, $offset)
                    );
    $offset = $this->context->structFieldMap[$rightVar->typeOf()->getElementType()->getName()]['length'];
                    $rightSize = $this->context->builder->load(
                        $this->context->builder->structGep($rightVar, $offset)
                    );
    $__right = $this->context->builder->intCast($rightSize, $leftSize->typeOf());
                            
                            
                        

                        

                        

                        

                        $size = $this->context->builder->addNoUnsignedWrap($leftSize, $__right);
    $this->context->builder->call(
                    $this->context->lookupFunction('__string__realloc') , 
                    $destVar
                    , $size
                    
                );
    $destValue = $this->context->builder->load($destVar);
    $offset = $this->context->structFieldMap[$destValue->typeOf()->getElementType()->getName()]['length'];
                $this->context->builder->store(
                    $size,
                    $this->context->builder->structGep($destValue, $offset)
                );
    $offset = $this->context->structFieldMap[$destValue->typeOf()->getElementType()->getName()]['value'];
                    $char = $this->context->builder->structGep($destValue, $offset);
    $offset = $this->context->structFieldMap[$leftVar->typeOf()->getElementType()->getName()]['value'];
                    $leftChar = $this->context->builder->structGep($leftVar, $offset);
    $this->context->intrinsic->memcpy($char, $leftChar, $leftSize, false);
    $char = $this->context->builder->gep(
                        $char,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $leftSize
                    );
    $offset = $this->context->structFieldMap[$rightVar->typeOf()->getElementType()->getName()]['value'];
                    $rightChar = $this->context->builder->structGep($rightVar, $offset);
    $this->context->intrinsic->memcpy($char, $rightChar, $rightSize, false);
    
        $this->context->builder->store($destValue, $dest->value);
    }

    private function copy(\gcc_jit_block_ptr $block, Variable $dest, Variable $other, \gcc_jit_rvalue_ptr $offset): void {
        $addr = \gcc_jit_lvalue_get_address(
            \gcc_jit_context_new_array_access(
                $this->context->context,
                $this->context->location(),
                $this->valuePtr($dest->rvalue),
                $offset
            ),
            $this->context->location()
        );
        switch ($other->type) {
            case Variable::TYPE_STRING:
                $this->context->memory->memcpy($block, $addr, $this->valuePtr($other->rvalue), $this->sizePtr($other->rvalue)->asRValue());
                break;
            case Variable::TYPE_NATIVE_LONG:
                $this->context->helper->eval(
                    $block,
                    $this->context->helper->call(
                        'sprintf',
                        $addr,
                        $this->context->constantFromString('%lld'),
                        $other->rvalue
                    )
                );
                break;
            default:
                throw new \LogicException("Unhandled type for copy $other->type");
        }
    }

    public function dimFetch(\gcc_jit_rvalue_ptr $str, \gcc_jit_rvalue_ptr $dim): \gcc_jit_rvalue_ptr {
        return \gcc_jit_lvalue_get_address(\gcc_jit_context_new_array_access(
            $this->context->context,
            $this->context->location(),
            $this->strCharConsts->asRValue(),
            $this->context->helper->cast(
                \gcc_jit_context_new_array_access(
                    $this->context->context,
                    $this->context->location(),
                    $this->valuePtr($str),
                    $this->context->helper->cast(
                        $dim,
                        'size_t'
                    )
                )->asRValue(),
                'size_t'
            )
        ), $this->context->location());
    }

}