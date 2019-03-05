<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

use PHPCompiler\Handler;

use PHPCfg\Operand;

class Helper {

    private Context $context;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function eval(\gcc_jit_block_ptr $block, \gcc_jit_rvalue_ptr $value): void {
        \gcc_jit_block_add_eval($block, $this->context->location(), $value);
    }

    public function call(
        string $func, 
        ?\gcc_jit_rvalue_ptr ...$params
    ): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_call(
            $this->context->context,
            $this->context->location(),
            $this->context->lookupFunction($func)->func,
            count($params),
            \gcc_jit_rvalue_ptr_ptr::fromArray(
                ...$params
            )
        );
    }

    public function cast(\gcc_jit_rvalue_ptr $from, string $to): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_cast(
            $this->context->context,
            $this->context->location(),
            $from,
            $this->context->getTypeFromString($to)
        );
    }

    public function numericBinaryOp(
        int $op,
        Operand $result,
        Variable $left,
        Variable $right 
    ): \gcc_jit_rvalue_ptr {
        if ($left->type === $right->type) {
            switch ($left->type) {
                case Variable::TYPE_NATIVE_LONG:
                    $resultType = Variable::TYPE_NATIVE_LONG;
                    $rvalue = $this->binaryOp(
                        $op,
                        Variable::getStringType(Variable::TYPE_NATIVE_LONG),
                        $left->rvalue,
                        $right->rvalue
                    );
                    break;
                default:
                    throw new \LogicException("Unhandled type: " . $left->type);
            }
        } else {
            throw new \LogicException("Unhandled type pair: " . $left->type . ' and ' . $right->type);
        }
        $neededResultType = Variable::getTypeFromType($result->type);
        if ($neededResultType === $resultType) {
            return $rvalue;
        }
        // Need to cast
        throw new \LogicException("Unhandled type cast needed for " . $neededResultType . " from " . $resultType);
    }

    public function binaryOp(
        int $op, 
        string $type, 
        \gcc_jit_rvalue_ptr $left,
        \gcc_jit_rvalue_ptr $right
    ): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_binary_op(
            $this->context->context, 
            $this->context->location(), 
            $op, 
            $this->context->getTypeFromString($type), 
            $left,
            $right
        );
    }

    public function compareOp(
        int $op,
        Operand $result,
        Variable $left,
        Variable $right 
    ): \gcc_jit_rvalue_ptr {
        if ($left->type === $right->type) {
            switch ($left->type) {
                case Variable::TYPE_NATIVE_LONG:
                    $rvalue = \gcc_jit_context_new_comparison(
                        $this->context->context,
                        $this->context->location(),
                        $op,
                        $left->rvalue,
                        $right->rvalue
                    );
                    break;
                default:
                    throw new \LogicException("Unhandled type: " . $left->type);
            }
        } else {
            throw new \LogicException("Unhandled type pair: " . $left->type . ' and ' . $right->type);
        }
        $neededResultType = Variable::getTypeFromType($result->type);
        if ($neededResultType === Variable::TYPE_NATIVE_BOOL) {
            return $rvalue;
        }
        // Need to cast
        throw new \LogicException("Unhandled type cast needed for " . $neededResultType . " from " . $resultType);
    }

    public function importFunction(
        string $funcName, 
        string $returnType, 
        bool $isVariadic, 
        string ...$params
    ): void {
        $this->context->registerFunction($funcName, $this->createFunction(
            \GCC_JIT_FUNCTION_IMPORTED,
            $funcName,
            $returnType,
            $isVariadic,
            ...$params
        ));
    }

    public function createFunction(
        int $type, 
        string $funcName, 
        string $returnType, 
        bool $isVariadic, 
        string ...$params
    ): Func {
        $paramPointers = [];
        $i = 0;
        foreach ($params as $param) {
            $paramPointers[] = \gcc_jit_context_new_param (
                $this->context->context, 
                null, 
                $this->context->getTypeFromString($param), 
                "{$funcName}_{$i}"
            );
            $i++;
        }
        $gccReturnType = $this->context->getTypeFromString($returnType);
        return new Func(
            $funcName,
            \gcc_jit_context_new_function(
                $this->context->context, 
                $this->context->location(),
                $type,
                $gccReturnType,
                $funcName,
                count($paramPointers), 
                \gcc_jit_param_ptr_ptr::fromArray(
                    ...$paramPointers
                ),
                $isVariadic ? 1 : 0
            ),
            $gccReturnType,
            ...$paramPointers
        );
    }

    public function assign(
        \gcc_jit_block_ptr $block,
        \gcc_jit_lvalue_ptr $result,
        \gcc_jit_rvalue_ptr $value
    ): void {
        \gcc_jit_block_add_assignment(
            $block,
            $this->context->location(),
            $result,
            $value
        );
    }

    public function assignOperand(
        \gcc_jit_block_ptr $block,
        Operand $result,
        Variable $value
    ): void {
        if (empty($result->usages) && !$this->context->scope->variables->contains($result)) {
            // optimize out assignment
            return;
        }
        $result = $this->context->getVariableFromOp($result);
        if ($value->type === $result->type) {
            if ($value->type === Variable::TYPE_STRING) {
                $this->context->refcount->delref($block, $result->rvalue);
                $this->context->refcount->addref($block, $value->rvalue);
            }
            $this->assign(
                $block,
                $result->lvalue,
                $value->rvalue
            );
            return;
        }
        throw new \LogicException("Assignment of different types not supported yet");
    }

    public function createField(string $name, string $type): \gcc_jit_field_ptr {
        return \gcc_jit_context_new_field(
            $this->context->context,
            $this->context->location(),
            $this->context->getTypeFromString($type),
            $name
        );
    }

}