<?php

# This file is generated, changes you make will be lost.
# Make your changes in /home/ircmaxell/Workspace/PHP-Compiler/PHP-Compiler/script/../lib/JIT/Helper.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

use PHPCompiler\OpCode;
use PHPLLVM;
use PHPLLVM\Builder;

class Helper {
    
    public Context $context;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function unaryOp(OpCode $opcode, Variable $var): Variable {
        $varValue = $this->loadValue($var);
        switch ($var->type) {
            case Variable::TYPE_NATIVE_LONG:
                switch ($opcode->type) {
                    case OpCode::TYPE_UNARY_MINUS:
                        $result = $this->context->builder->negate($varValue);
                        goto return_long;
                }
                break;
            case Variable::TYPE_NATIVE_DOUBLE:
                switch ($opcode->type) {
                    case OpCode::TYPE_UNARY_MINUS:
                        $result = $this->context->builder->fNegate($varValue);
                        goto return_double;
                }
                break;
        }
        $type = $opcode->getType();
        throw new \LogicException("Reached end of switch, can't handle unary operation yet: $type for type {$var->type}");
return_double:
        return new Variable($this->context, Variable::TYPE_NATIVE_DOUBLE, Variable::KIND_VALUE, $result);
return_long:
        return new Variable($this->context, Variable::TYPE_NATIVE_LONG, Variable::KIND_VALUE, $result);
return_bool:
        return new Variable($this->context, Variable::TYPE_NATIVE_BOOL, Variable::KIND_VALUE, $result);
    }

    public function binaryOp(OpCode $opcode, Variable $left, Variable $right): Variable {
        $leftValue = $this->loadValue($left);
        $rightValue = $this->loadValue($right);
        $leftType = $left->type;
        $rightType = $right->type;
restart:
        switch (type_pair($leftType, $rightType)) {
            case TYPE_PAIR_NATIVE_LONG_NATIVE_DOUBLE:
                $leftType = Variable::TYPE_NATIVE_DOUBLE;
                $leftValue = $this->context->builder->siToFp($leftValue, $rightValue->typeOf());
                goto restart;
            case TYPE_PAIR_NATIVE_DOUBLE_NATIVE_LONG:
                $rightType = Variable::TYPE_NATIVE_DOUBLE;
                $rightValue = $this->context->builder->siToFp($rightValue, $leftValue->typeOf());
                goto restart;
            case TYPE_PAIR_NATIVE_DOUBLE_NATIVE_DOUBLE:
                switch ($opcode->type) {
                    case OpCode::TYPE_MUL:
                        $result = $this->context->builder->fmul($leftValue, $rightValue);
                        goto return_double;
                    case OpCode::TYPE_PLUS:
                        $result = $this->context->builder->fadd($leftValue, $rightValue);
                        goto return_double;
                    case OpCode::TYPE_MINUS:
                        $result = $this->context->builder->fsub($leftValue, $rightValue);
                        goto return_double;
                    case OpCode::TYPE_DIV:
                        $result = $this->context->builder->fdiv($leftValue, $rightValue);
                        goto return_double;
                    case OpCode::TYPE_MODULO:
                        $result = $this->context->builder->frem($leftValue, $rightValue);
                        goto return_double;
                    case OpCode::TYPE_BITWISE_AND:
                    case OpCode::TYPE_BITWISE_OR:
                    case OpCode::TYPE_BITWISE_XOR:
                        break;
                    case OpCode::TYPE_GREATER_OR_EQUAL:
                        $result = $this->context->builder->fcmp(Builder::REAL_OGE, $leftValue, $rightValue);
                        goto return_bool;
                    case OpCode::TYPE_SMALLER_OR_EQUAL:
                        $result = $this->context->builder->fcmp(Builder::REAL_OLE, $leftValue, $rightValue);
                        goto return_bool;
                    case OpCode::TYPE_GREATER:
                        $result = $this->context->builder->fcmp(Builder::REAL_OGT, $leftValue, $rightValue);
                        goto return_bool;
                    case OpCode::TYPE_SMALLER:
                        $result = $this->context->builder->fcmp(Builder::REAL_OLT, $leftValue, $rightValue);
                        goto return_bool;
                    case OpCode::TYPE_IDENTICAL:
                    case OpCode::TYPE_EQUAL:
                        $result = $this->context->builder->fcmp(Builder::REAL_OEQ, $leftValue, $rightValue);
                        goto return_bool;
                    case OpCode::TYPE_NOT_EQUAL:
                        $result = $this->context->builder->fcmp(Builder::REAL_ONE, $leftValue, $rightValue);
                        goto return_bool;
                }
                break;
            case TYPE_PAIR_NATIVE_LONG_NATIVE_LONG:
                switch ($opcode->type) {
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
                    case OpCode::TYPE_EQUAL:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        $result = $this->context->builder->icmp(PHPLLVM\Builder::INT_EQ, $leftValue, $__right);
    
                        goto return_bool;
                    case OpCode::TYPE_NOT_EQUAL:
                        $__right = $this->context->builder->intCast($rightValue, $leftValue->typeOf());
                            
                            
                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        $result = $this->context->builder->icmp(PHPLLVM\Builder::INT_NE, $leftValue, $__right);
    
                        goto return_bool;
                }
                break;
        }
        $type = $opcode->getType();
        throw new \LogicException("Reached end of switch, can't handle binary operation yet: $type for type pair {$leftType} and {$rightType}");
return_double:
        return new Variable($this->context, Variable::TYPE_NATIVE_DOUBLE, Variable::KIND_VALUE, $result);
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