<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/lib/JIT/Builtin/Type/Value.pre instead.

// First, expand statements
)
}



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

class Value extends Type {

    public function register(): void {
        

        

        $struct___cfcd208495d565ef66e7dff9f98764da = $this->context->context->namedStructType('__value__');
            // declare first so recursive structs are possible :)
            $this->context->registerType('__value__', $struct___cfcd208495d565ef66e7dff9f98764da);
            $this->context->registerType('__value__' . '*', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0));
            $this->context->registerType('__value__' . '**', $struct___cfcd208495d565ef66e7dff9f98764da->pointerType(0)->pointerType(0));

            $struct___cfcd208495d565ef66e7dff9f98764da->setBody(
                false ,  // packed
                $this->context->getTypeFromString('__ref__')
                , $this->context->getTypeFromString('int8')
                , $this->context->getTypeFromString('int8[8]')
                
            );
            
            $this->context->structFieldMap['__value__'] = [
                'ref' => 0
                , 'type' => 1
                , 'value' => 2
                
            ];
        
    
    }

    public function implement(): void {
    }

    public function initialize(): void {
    }

    public function castToLong(PHPLLVM\Value $value): Value {
        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
                    $type = $this->context->builder->load(
                        $this->context->builder->structGep($value, $offset)
                    );

            

            

            

        $__switches[] = $__switch = new \StdClass;
                $__switch->type = $type->typeOf();
                $__prev = $this->context->builder->getInsertBlock();
                $__switch->default = $__prev->insertBasicBlock('default');
                $__prev->moveBefore($__switch->default);
                $__switch->end = $__switch->default->insertBasicBlock('end');
                $__switch->numCases = 0;
                $__switch->numCases++;
                
                $this->context->builder->branchSwitch($type, $__switch->default, $__switch->numCases);
                $__case = end($__switches)->default->insertBasicBlock('case_' . 0);
                    $this->context->builder->positionAtEnd($__case);
                    { $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
                    $ptr = $this->context->builder->structGep($value, $offset);

            

            

            

        $resultPtr = $this->context->builder->pointerCast(
                        $ptr, 
                        $this->context->getTypeFromString('int64*')
                    );

            

            

            

        $offset = $this->context->getTypeFromString('int32')->constInt(0, false);

            

            

            

        $result = $this->context->builder->load($this->context->builder->gep(
                        $ptr,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $offset
                    ));

            

            

            

        $this->context->builder->returnValue($result);

            

            

            

         }
                    if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                        $this->context->builder->branch(end($__switches)->end);
                    }
                
                $this->context->builder->positionAtEnd(end($__switches)->default);
                { $result = $this->context->getTypeFromString('int64')->constInt(0, false);

            

            

            

        $this->context->builder->returnValue($result);

            

            

            

         }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($__switches)->end);
                }
                $this->context->builder->positionAtEnd(end($__switches)->end);
                array_pop($__switches);

            

            

            

        
    }

    public function writeLong(PHPLLVM\Value $value, int $value): void {
        $type = $this->context->getTypeFromString('int8')->constInt(Variable::TYPE_NATIVE_LONG, false);

            

            

            

        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['type'];
                $this->context->builder->store(
                    $type,
                    $this->context->builder->structGep($value, $offset)
                );

            

            

            

        $offset = $this->context->structFieldMap[$value->typeOf()->getElementType()->getName()]['value'];
                    $ptr = $this->context->builder->structGep($value, $offset);

            

            

            

        $resultPtr = $this->context->builder->pointerCast(
                        $ptr, 
                        $this->context->getTypeFromString('int64*')
                    );

            

            

            

        $offset = $this->context->getTypeFromString('int32')->constInt(0, false);

            

            

            

        $type = $this->context->getTypeFromString('int64');
                    if (!is_object($value)) {
                        $result = $type->constInt($value, false);
                    } elseif ($value->typeOf()->getWidth() >= $type->getWidth()) {
                        $result = $this->context->builder->truncOrBitCast($value, $type);
                    } else {
                        $result = $this->context->builder->zExtOrBitCast($value, $type);
                    }

            

            

            

        $this->context->builder->store($result, $this->context->builder->gep(
                        $ptr,
                        //$this->context->context->int32Type()->constInt(0, false),
                        //$this->context->context->int32Type()->constInt(0, false),
                        $offset
                    ));

            

            

            

        
    }

}