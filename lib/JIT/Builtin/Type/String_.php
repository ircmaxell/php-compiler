<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/lib/JIT/Builtin/Type/String_.pre instead.

// First, expand statements
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
            $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false ,  // packed
                $this->context->getTypeFromString('__ref__')
                , $this->context->getTypeFromString('int64')
                , $this->context->getTypeFromString('int8')
                
            );
            $this->context->registerType('__string__', $struct___cfcd208495d565ef66e7dff9f98764da);
            $this->context->registerType('__string__' . '*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
            $this->context->registerType('__string__' . '**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));
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
        // $this->implementRealloc();
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
                    $struct = $this->context->memory->mallocWithExtra($type, $allocSize);
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
        $realloc = $this->context->lookupFunction('__string__realloc');
        $block = \gcc_jit_function_new_block($realloc->func, 'main');
        $isnull = \gcc_jit_function_new_block($realloc->func, 'is_null');
        $notnull = \gcc_jit_function_new_block($realloc->func, 'not_null');
        
        $ptr = $realloc->params[0]->asLValue();
        $reallocSize = $this->context->helper->binaryOp(
            GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $this->size->asRValue(),
            $realloc->params[1]->asRValue()
        );
        $local = \gcc_jit_function_new_local($realloc->func, null, $this->pointer, 'result');
        $this->context->helper->assign(
            $block, 
            $local,
            $this->context->memory->realloc($ptr->asRValue(), $reallocSize, $this->pointer) 
        );
        $this->context->helper->assign(
            $block,
            $this->writeSize($local->asRValue()),
            $realloc->params[1]->asRValue()
        );
        gcc_jit_block_end_with_conditional(
            $block,
            $this->context->location(),
            gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                \GCC_JIT_COMPARISON_EQ,
                $ptr->asRValue(),
                $this->nullPointer()
            ),
            $isnull,
            $notnull
        );
        $this->context->refcount->init($isnull, $local->asRValue(), Refcount::TYPE_INFO_REFCOUNTED | Refcount::TYPE_INFO_TYPE_STRING);

        \gcc_jit_block_end_with_jump($isnull,  null,$notnull);
        \gcc_jit_block_end_with_return($notnull,  null, $local->asRValue());
    }

    private function implementSeparate(): void {
        $fn___8f14e45fceea167a5a36dedd4bea2543 = $this->context->lookupFunction('__string__separate');
    $block___8f14e45fceea167a5a36dedd4bea2543 = $fn___8f14e45fceea167a5a36dedd4bea2543->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___8f14e45fceea167a5a36dedd4bea2543);
    $string = $fn___8f14e45fceea167a5a36dedd4bea2543->getParam(0);
    
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
    $this->context->intrinsic->memcpy($dest, $char, $size, false);
    $offset = $this->context->structFieldMap[$string->typeOf()->getElementType()->getName()]['ref'];
                    $ref = $this->context->builder->load(
                        $this->context->builder->structGep($string, $offset)
                    );
    $offset = $this->context->structFieldMap[$ref->typeOf()->getName()]['typeinfo'];
                    $typeinfo = $this->context->builder->extractValue($ref, $offset);
    $virtual = $this->context->builder->pointerCast(
                        $new, 
                        $this->context->getTypeFromString('__ref__virtual*')
                    );
    $this->context->builder->call(
                    $this->context->lookupFunction('__ref__init') , 
                    $typeinfo
                    , $virtual
                    
                );
    $this->context->builder->returnValue($new);
    
    $this->context->builder->clearInsertionPosition();
        return;

        $func = $this->context->lookupFunction('__string__separate');
        $virtual = $func->params[0]->asRValue();
        $deref = \gcc_jit_rvalue_dereference($virtual, $this->context->location());
        $from = $this->context->helper->cast(
            $deref->asRValue(),
            '__string__*'
        );
        $block = \gcc_jit_function_new_block($func->func, 'main');
        $tmp = \gcc_jit_function_new_local($func->func, $this->context->location(), $this->pointer, 'tmp');
        $length = $this->sizePtr($from)->asRValue();
        $this->init(
            $block,
            $tmp,
            $this->context->helper->cast($this->valuePtr($from), 'const char*'),
            $length
        );
        \gcc_jit_block_add_assignment(
            $block,
            $this->context->location(),
            $deref,
            \gcc_jit_context_new_cast(
                $this->context->context,
                $this->context->location(),
                $tmp->asRValue(),
                $this->context->refcount->pointer
            )
        );
        \gcc_jit_block_end_with_void_return($block, $this->context->location());   
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
        $this->context->helper->assign(
            $block,
            $dest,
            $this->context->helper->call(
                '__string__alloc',
                $length
            )
        );
        if ($isConstant) {
            // disable refcount
            $this->context->refcount->disableRefcount($block, $dest->asRValue());
        }
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

    public function concat(\gcc_jit_block_ptr $block, Variable $dest, Variable $left, Variable $right): void {
        assert($dest->type === Variable::TYPE_STRING);
        $this->context->refcount->separate($block, $dest->lvalue);


        $leftSize = $this->size($left);
        $rightSize = $this->size($right);
        $this->reallocate($block, $dest->lvalue, $this->context->helper->binaryOp(
            \GCC_JIT_BINARY_OP_PLUS,
            'size_t',
            $leftSize,
            $rightSize
        ));

        if ($left !== $dest) {
            $this->copy(
                $block, 
                $dest, 
                $left,
                $this->context->constantFromInteger(0, 'size_t')
            );
        }
        $this->copy(
            $block,
            $dest,
            $right,
            $this->context->helper->binaryOp(
                \GCC_JIT_BINARY_OP_MINUS,
                'size_t',
                $this->size($dest),
                $this->size($right)
            )
        );
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