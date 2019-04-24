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
            $two = 2;

            $fn___c81e728d9d4c2f636f067f89cc14862c = $this->context->lookupFunction('__mm__malloc');
    $block___c81e728d9d4c2f636f067f89cc14862c = $fn___c81e728d9d4c2f636f067f89cc14862c->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___c81e728d9d4c2f636f067f89cc14862c);
    $size = $fn___c81e728d9d4c2f636f067f89cc14862c->getParam(0);
    
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
            $fn___a87ff679a2f3e71d9181a67b7542122c = $this->context->lookupFunction('__mm__realloc');
    $block___a87ff679a2f3e71d9181a67b7542122c = $fn___a87ff679a2f3e71d9181a67b7542122c->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___a87ff679a2f3e71d9181a67b7542122c);
    $void = $fn___a87ff679a2f3e71d9181a67b7542122c->getParam(0);
    $size = $fn___a87ff679a2f3e71d9181a67b7542122c->getParam(1);
    
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
            $fn___1679091c5a880faf6fb5e6087eb1b2dc = $this->context->lookupFunction('__mm__free');
    $block___1679091c5a880faf6fb5e6087eb1b2dc = $fn___1679091c5a880faf6fb5e6087eb1b2dc->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___1679091c5a880faf6fb5e6087eb1b2dc);
    $void = $fn___1679091c5a880faf6fb5e6087eb1b2dc->getParam(0);
    
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
            $fn___c9f0f895fb98ab9159f51fd0297e236d = $this->context->lookupFunction('__mm__malloc');
    $block___c9f0f895fb98ab9159f51fd0297e236d = $fn___c9f0f895fb98ab9159f51fd0297e236d->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___c9f0f895fb98ab9159f51fd0297e236d);
    $size = $fn___c9f0f895fb98ab9159f51fd0297e236d->getParam(0);
    
    $result = $this->context->builder->call(
                        $this->context->lookupFunction('_emalloc') , 
                        $size
                        
                    );
    $this->context->builder->returnValue($result);
    
    $this->context->builder->clearInsertionPosition();
            $fn___d3d9446802a44259755d38e6d163e820 = $this->context->lookupFunction('__mm__realloc');
    $block___d3d9446802a44259755d38e6d163e820 = $fn___d3d9446802a44259755d38e6d163e820->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___d3d9446802a44259755d38e6d163e820);
    $void = $fn___d3d9446802a44259755d38e6d163e820->getParam(0);
    $size = $fn___d3d9446802a44259755d38e6d163e820->getParam(1);
    
    $result = $this->context->builder->call(
                        $this->context->lookupFunction('_erealloc') , 
                        $void
                        , $size
                        
                    );
    $this->context->builder->returnValue($result);
    
    $this->context->builder->clearInsertionPosition();
            $fn___c20ad4d76fe97759aa27a0c99bff6710 = $this->context->lookupFunction('__mm__free');
    $block___c20ad4d76fe97759aa27a0c99bff6710 = $fn___c20ad4d76fe97759aa27a0c99bff6710->appendBasicBlock('main');
    $this->context->builder->positionAtEnd($block___c20ad4d76fe97759aa27a0c99bff6710);
    $void = $fn___c20ad4d76fe97759aa27a0c99bff6710->getParam(0);
    
    $this->context->builder->call(
                    $this->context->lookupFunction('_efree') , 
                    $void
                    
                );
    $this->context->builder->returnVoid();
    
    $this->context->builder->clearInsertionPosition();
        }
    }
}
