<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

use PHPCompiler\Block;
use PHPCfg\Operand;
use PHPTypes\Type;
use PHPCompiler\VM\Variable as VMVariable;

final class Variable {
    const TYPE_NATIVE_LONG = 1;
    const TYPE_NATIVE_BOOL = 2;
    const TYPE_NATIVE_DOUBLE = 3;
    const TYPE_STRING = 4 | self::IS_REFCOUNTED;
    const TYPE_OBJECT = 5 | self::IS_REFCOUNTED;
    const TYPE_VALUE = 6;
    const TYPE_HASHTABLE = 7 | self::IS_REFCOUNTED;

    const TYPE_MAP = [
        Type::TYPE_DOUBLE => self::TYPE_NATIVE_DOUBLE,
        Type::TYPE_LONG => self::TYPE_NATIVE_LONG,
        Type::TYPE_BOOLEAN => self::TYPE_NATIVE_BOOL,
        Type::TYPE_STRING => self::TYPE_STRING,
        Type::TYPE_OBJECT => self::TYPE_OBJECT,
        Type::TYPE_ARRAY => self::TYPE_HASHTABLE,
    ];

    const NATIVE_TYPE_MAP = [
        self::TYPE_NATIVE_LONG => 'long long',
        self::TYPE_NATIVE_BOOL => 'bool',
        self::TYPE_NATIVE_DOUBLE => 'double',
        self::TYPE_STRING => '__string__*',
        self::TYPE_OBJECT => '__object__*',
        self::TYPE_VALUE => '__value__',
        self::TYPE_HASHTABLE => '__hashtable__*',
    ];

    const IS_NATIVE_ARRAY = 1 << 6;
    const IS_REFCOUNTED   = 1 << 7;

    public int $type;

    const KIND_VARIABLE = 1;
    const KIND_VALUE = 2;
    public int $kind;

    public \gcc_jit_rvalue_ptr $rvalue;
    public ?\gcc_jit_lvalue_ptr $lvalue = null;
    private Context $context;

    private static int $lvalueCounter = 0;
    public int $nextFreeElement = 0;

    public function __construct(
        Context $context, 
        int $type, 
        int $kind, 
        \gcc_jit_rvalue_ptr $rvalue, 
        ?\gcc_jit_lvalue_ptr $lvalue
    ) {
        $this->context = $context;
        $this->type = $type;
        $this->kind = $kind;
        $this->rvalue = $rvalue;
        $this->lvalue = $lvalue;
    }

    public static function fromVMVariable(int $type): int {
        switch ($type) {
            case VMVariable::TYPE_INTEGER: return self::TYPE_NATIVE_LONG;
            case VMVariable::TYPE_FLOAT: return self::TYPE_NATIVE_DOUBLE;
            case VMVariable::TYPE_BOOLEAN: return self::TYPE_NATIVE_BOOL;
            case VMVariable::TYPE_STRING: return self::TYPE_STRING;
        }
        throw new \LogicException("Not implemented type conversion: $type");
    }

    public static function getStringTypeFromType(Type $type): string {
        return self::getStringType(self::getTypeFromType($type));
    }

    public static function getStringType(int $type): string {
        if (isset(self::NATIVE_TYPE_MAP[$type])) {
            return self::NATIVE_TYPE_MAP[$type];
        }
    }

    public static function getTypeFromType(Type $type): int {
        if (isset(self::TYPE_MAP[$type->type])) {
            return self::TYPE_MAP[$type->type];
        }
        if ($type->type === Type::TYPE_OBJECT) {
            return self::TYPE_OBJECT;
        }
        if ($type->type === Type::TYPE_ARRAY) {
            return self::TYPE_HASHTABLE;
        }
        return self::TYPE_VALUE;
    }

    /**
     * Returns a writable variable (lvalue)
     */
    public static function fromOp(
        Context $context,
        \gcc_jit_function_ptr $func,
        \gcc_jit_block_ptr $gccBlock,
        Block $block,
        Operand $op
    ): Variable {
        $type = self::getTypeFromType($op->type);
        $stringType = self::getStringType($type);
        if ($type === self::TYPE_HASHTABLE) {
            // see if it can be converted into a native array
            if (!$context->analyzer->canEscape($op)) {
                $size = $context->analyzer->computeStaticArraySize($op);
                if (!is_null($size) && !$context->analyzer->hasDynamicArrayAppend($op, $size)) {
                    $origType = self::getTypeFromType($op->type->subTypes[0]);
                    $type = self::IS_NATIVE_ARRAY | $origType;
                    $stringType = self::getStringType($origType) . '[' . $size . ']';
                }
            }
        }
        $lval = \gcc_jit_function_new_local(
            $func,
            $context->location(),
            $context->getTypeFromString($stringType),
            "lvalue_" . (++self::$lvalueCounter)
        );
        return new Variable(
            $context,
            $type,
            self::KIND_VARIABLE,
            \gcc_jit_lvalue_as_rvalue($lval),
            $lval
        );
    }

    /**
     * Returns a readable variable (rvalue)
     */
    public static function fromRValueOp(
        Context $context,
        \gcc_jit_rvalue_ptr $rvalue,
        Operand $op
    ): Variable {
        $type = self::getTypeFromType($op->type);
        $gccType = $context->getTypeFromString(self::NATIVE_TYPE_MAP[$type]);
        assert(\gcc_jit_rvalue_get_type($rvalue)->equals($gccType));
        return new Variable(
            $context,
            $type,
            self::KIND_VALUE,
            $rvalue,
            null
        );
    }

    public static function fromLiteral(Context $context, Operand $op): Variable {
        $type = self::getTypeFromType($op->type);
        switch ($type) {
            case self::TYPE_NATIVE_LONG:
                $rvalue = $context->constantFromInteger($op->value, self::getStringType($type));
                break;
            case self::TYPE_STRING:
                $rvalue = $context->constantStringFromString($op->value);
                break;
            case self::TYPE_NATIVE_DOUBLE:
                $rvalue = $context->constantFromFloat($op->value, self::getStringType($type));
                break;
            case self::TYPE_NATIVE_BOOL:
                $rvalue = $context->constantFromInteger($op->value ? 1 : 0, self::getStringType($type));
                break;
            default:
                throw new \LogicException("Literal type " . self::getStringType($type) . " not yet supported");
        }
        return new Variable(
            $context,
            $type,
            self::KIND_VALUE,
            $rvalue,
            null
        );
    }

    public static function fromConstantInt(Context $context, int $value): Variable {
        return new Variable(
            $context,
            self::TYPE_NATIVE_LONG,
            self::KIND_VALUE,
            $context->constantFromInteger($value),
            null
        );
    }

    public function castTo(int $type): self {
        switch ($type) {
            case self::TYPE_NATIVE_LONG:
            case self::TYPE_NATIVE_BOOL:
            case self::TYPE_NATIVE_DOUBLE:
                return new self(
                    $this->context,
                    $type,
                    self::KIND_VALUE,
                    $this->context->helper->cast($this->rvalue, self::getStringType($type)),
                    null
                );
            default:
                throw new \LogicException('Unhandlable cast operation to type: ' . $type);
        }
    }

    public function addref(\gcc_jit_block_ptr $block): void {
        if ($this->type & self::IS_REFCOUNTED) {
            $this->context->refcount->addref($block, $this->rvalue);
        }
    }

    public function free(\gcc_jit_block_ptr $block): void {
        if ($this->kind === self::KIND_VALUE) {
            return;
        }
        switch ($this->type) {
            case self::TYPE_NATIVE_LONG:
            case self::TYPE_NATIVE_BOOL:
            case self::TYPE_NATIVE_DOUBLE:
                return;
        }
        if ($this->type === self::TYPE_VALUE) {
            // TODO: free owned resources
            return;
        }
        if ($this->type & self::IS_NATIVE_ARRAY) {
            // free each
            for ($i = 0; $i < $this->nextFreeElement; $i++) {
                $this->dimFetch(self::fromConstantInt($this->context, $i))->free($block);
            }
            return;
        }
        if ($this->type & self::IS_REFCOUNTED) {
            $this->context->refcount->delref($block, $this->rvalue);
            return;
        }
        throw new \LogicException('Unknown free type: ' . $this->type);
    }

    public function initialize(\gcc_jit_block_ptr $block): void {
        if ($this->kind === self::KIND_VALUE) {
            return;
        }
        switch ($this->type) {
            case self::TYPE_STRING:
                // assign to null
                \gcc_jit_block_add_assignment(
                    $block,
                    $this->context->location(),
                    $this->lvalue,
                    \gcc_jit_context_null($this->context->context, $this->context->getTypeFromString(self::getStringType($this->type)))
                );
                //$this->context->type->string->allocate($block, $this->lvalue, $this->context->constantFromInteger(0, 'size_t'));
                break;
        }
    }
    
    public function toString(\gcc_jit_block_ptr $block): Variable {
        switch ($this->type) {
            case self::TYPE_STRING:
                return $this;
        }
    }

    public function dimFetch(self $dim): Variable {
        switch ($this->type) {
            case self::TYPE_STRING:
                $ptr = $this->context->type->string->dimFetch($this->rvalue, $dim->rvalue);
                return new Variable(
                    $this->context,
                    self::TYPE_STRING,
                    self::KIND_VALUE,
                    $ptr,
                    null
                );
            default:
                if (!$this->type & self::IS_NATIVE_ARRAY) {
                    throw new \LogicException("Unsupported dim fetch on " . self::getStringType($this->type));
                }
                $offset = $dim->castTo(self::TYPE_NATIVE_LONG);
                $ptr = \gcc_jit_context_new_array_access(
                    $this->context->context,
                    $this->context->location(),
                    $this->rvalue,
                    $offset->rvalue
                );
                return new Variable(
                    $this->context,
                    $this->type & (~self::IS_NATIVE_ARRAY),
                    self::KIND_VARIABLE,
                    $ptr->asRValue(),
                    $ptr
                );
        }
    }
}

const TYPE_PAIR_NATIVE_LONG_NATIVE_LONG = (Variable::TYPE_NATIVE_LONG << 16) | Variable::TYPE_NATIVE_LONG;
const TYPE_PAIR_NATIVE_DOUBLE_NATIVE_DOUBLE = (Variable::TYPE_NATIVE_DOUBLE << 16) | Variable::TYPE_NATIVE_DOUBLE;
const TYPE_PAIR_NATIVE_LONG_NATIVE_DOUBLE = (Variable::TYPE_NATIVE_LONG << 16) | Variable::TYPE_NATIVE_DOUBLE;
const TYPE_PAIR_NATIVE_DOUBLE_NATIVE_LONG = (Variable::TYPE_NATIVE_DOUBLE << 16) | Variable::TYPE_NATIVE_LONG;

function type_pair(int $left, int $right): int {
    return ($left << 16) | $right;
}