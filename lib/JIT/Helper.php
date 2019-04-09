<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/lib/JIT/Helper.pre instead.

// First, expand statements
/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

use PHPCompiler\OpCode;
use PHPLLVM;

class Helper {
    
    public Context $context;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function binaryOp(int $opcode, Variable $left, Variable $right): Variable {
        $leftValue = $this->loadValue($left);
        $rightValue = $this->loadValue($right);
        switch (type_pair($left->type, $right->type)) {
            case TYPE_PAIR_NATIVE_LONG_NATIVE_LONG:
                switch ($opcode) {
                    case OpCode::TYPE_MUL:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        
                            $result = $this->context->builder->mulNoSignedWrap($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_PLUS:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        
                            $result = $this->context->builder->addNoSignedWrap($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_MINUS:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        
                            $result = $this->context->builder->subNoSignedWrap($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_DIV:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        
                            $result = $this->context->builder->signedDiv($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_MODULO:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $result = $this->context->builder->signedRem($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_BITWISE_AND:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        $result = $this->context->builder->bitwiseAnd($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_BITWISE_OR:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        $result = $this->context->builder->bitwiseOr($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_BITWISE_XOR:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        $result = $this->context->builder->bitwiseXor($leftValue, $__right);
    
                        goto return_long;
                    case OpCode::TYPE_GREATER_OR_EQUAL:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $cmp = PHPLLVM\Builder::INT_SGE;
                            
                            $result = $this->context->builder->icmp($cmp, $leftValue, $__right);
    
                        goto return_bool;
                    case OpCode::TYPE_SMALLER_OR_EQUAL:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $cmp = PHPLLVM\Builder::INT_SLE;
                            
                            $result = $this->context->builder->icmp($cmp, $leftValue, $__right);
    
                        goto return_bool;
                    case OpCode::TYPE_GREATER:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $cmp = PHPLLVM\Builder::INT_SGT;
                            
                            $result = $this->context->builder->icmp($cmp, $leftValue, $__right);
    
                        goto return_bool;
                    case OpCode::TYPE_SMALLER:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        
                            $cmp = PHPLLVM\Builder::INT_SLT;
                            
                            $result = $this->context->builder->icmp($cmp, $leftValue, $__right);
    
                        goto return_bool;
                    case OpCode::TYPE_IDENTICAL:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        $result = $this->context->builder->icmp(PHPLLVM\Builder::INT_EQ, $leftValue, $__right);
    
                        goto return_bool;
                    case OpCode::TYPE_EQUAL:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        $result = $this->context->builder->icmp(PHPLLVM\Builder::INT_NE, $leftValue, $__right);
    
                        goto return_bool;
                    default:
                        throw new \LogicException("Unknown integer-integer binary operation found: $opcode");
                }
        }
        throw new \LogicException("Reached end of switch, can't handle binary operation yet: $opcode");
return_long:
        return new Variable($this->context, Variable::TYPE_NATIVE_LONG, Variable::KIND_VALUE, $result);
return_bool:
        return new Variable($this->context, Variable::TYPE_NATIVE_BOOL, Variable::KIND_VALUE, $result);
    }

    public function loadValue(Variable $variable): PHPLLVM\Value {
        if ($variable->kind === Variable::KIND_VALUE) {
            return $variable->value;
        }
        return $this->context->builder->load($variable->value);
    }

}