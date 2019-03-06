<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\VM;

use PHPTypes\Type;
use PHPCompiler\OpCode;

class Variable {
    const TYPE_UNKNOWN = -1;
    const TYPE_NULL = 0;
    const TYPE_INTEGER = 1;
    const TYPE_FLOAT = 2;
    const TYPE_BOOLEAN = 3;
    const TYPE_STRING = 4;
    const TYPE_OBJECT = 5;
    const TYPE_INDIRECT = 6;


    const NUMERIC = self::TYPE_INTEGER | self::TYPE_FLOAT;

    public int $type = self::TYPE_NULL;

    private string $string;
    private int $integer;
    private float $float;
    private bool $bool;
    private ObjectEntry $object;
    private Variable $indirect;

    public ?int $typeConstraint = null;
    public ?string $classConstraint = null; 

    public function __construct(int $type = self::TYPE_NULL) {
        $this->type = $type;
    }

    public static function mapFromType(Type $type): int {
        switch ($type->type) {
            case Type::TYPE_NULL:
                return self::TYPE_NULL;
            case Type::TYPE_LONG:
                return self::TYPE_INTEGER;
            case Type::TYPE_DOUBLE:
                return self::TYPE_FLOAT;
            case Type::TYPE_BOOLEAN:
                return self::TYPE_BOOLEAN;
            case Type::TYPE_OBJECT:
                return self::TYPE_OBJECT;
            case Type::TYPE_STRING:
                return self::TYPE_STRING;
        }
        return self::TYPE_UNKNOWN;
    }

    public function resolveIndirect(): self {
        $var = $this;
        while ($var->type === self::TYPE_INDIRECT) {
            $var = $var->indirect;
        }
        return $var;
    }

    public function int(int $value): void {
        $this->reset();
        $this->type = self::TYPE_INTEGER;
        $this->integer = $value;
    }

    public function toInt(): int {
        switch ($this->type) {
            case self::TYPE_NULL:
                return 0;
            case self::TYPE_INTEGER:
                return $this->integer;
            case self::TYPE_FLOAT:
                return (int) $this->float;
            case self::TYPE_BOOLEAN:
                return $this->bool ? 1 : 0;
            case self::TYPE_STRING:
                return (int) $this->string;
            case self::TYPE_INDIRECT:
                return $this->indirect->toInt();
        }
    }

    public function float(float $value): void {
        $this->reset();
        $this->type = self::TYPE_FLOAT;
        $this->float = $value;
    }

    public function toFloat(): float {
        switch ($this->type) {
            case self::TYPE_NULL:
                return 0;
            case self::TYPE_INTEGER:
                return (float) $this->integer;
            case self::TYPE_FLOAT:
                return $this->float;
            case self::TYPE_BOOLEAN:
                return $this->bool ? 1.0 : 0.0;
            case self::TYPE_STRING:
                return (float) $this->string;
            case self::TYPE_INDIRECT:
                return $this->indirect->toFloat();
        }
    }

    public function toNumeric() {
        switch ($this->type) {
            case self::TYPE_NULL:
                return 0;
            case self::TYPE_INTEGER:
                return $this->integer;
            case self::TYPE_FLOAT:
                return $this->float;
            case self::TYPE_BOOLEAN:
                return $this->bool ? 1 : 0;
            case self::TYPE_STRING:
                if (!is_numeric($this->string)) {
                    throw new \LogicException("Cannot convert string to numeric");
                }
                if (((string)(int) $this->string) === $this->string) {
                    return (int) $this->string;
                }
                return (float) $this->string;
            case self::TYPE_INDIRECT:
                return $this->indirect->toNumeric();
        }
        throw new \LogicException("Not implemented numeric conversion: $this->type");
    }

    public function bool(bool $value): void {
        $this->reset();
        $this->type = self::TYPE_BOOLEAN;
        $this->bool = $value;
    }

    public function toBool(): bool {
        switch ($this->type) {
            case self::TYPE_NULL:
                return false;
            case self::TYPE_INTEGER:
                return 0 !== $this->integer;
            case self::TYPE_FLOAT:
                return 0 !== $this->float;
            case self::TYPE_BOOLEAN:
                return $this->bool;
            case self::TYPE_STRING:
                return '' === $this->string || '0' === $this->string;
            case self::TYPE_INDIRECT:
                return $this->indirect->toBool();
        }
    }

    public function string(string $value): void {
        $this->reset();
        $this->type = self::TYPE_STRING;
        $this->string = $value;
    }

    public function toString(): string {
        switch ($this->type) {
            case self::TYPE_STRING:
                return $this->string;
            case self::TYPE_INTEGER:
                return (string) $this->integer;
            case self::TYPE_FLOAT:
                return (string) $this->float;
            case self::TYPE_BOOLEAN:
                return $this->bool ? '1' : '';
            case self::TYPE_INDIRECT:
                return $this->indirect->toString();
        }
    }

    public function object(ObjectEntry $value): void {
        $this->reset();
        $this->type = self::TYPE_OBJECT;
        $this->object = $value;
    }

    public function toObject(): ObjectEntry {
        switch ($this->type) {
            case self::TYPE_OBJECT:
                return $this->object;
            case self::TYPE_INDIRECT:
                return $this->indirect->toObject();
        }
        throw new \LogicException("Cannot convert $this->type to Object");
    }

    public function indirect(Variable $value): void {
        $this->reset();
        $this->type = self::TYPE_INDIRECT;
        $this->indirect = $value;
    }

    public function reset(): void {
        $this->type = self::TYPE_NULL;
        unset($this->string);
        unset($this->integer);
        unset($this->float);
        unset($this->bool);
        unset($this->object);
        unset($this->indirect);
    }

    public function castFrom(int $type, self $var) {
        $this->reset();
        $this->type = $type;
        switch ($type) {
            case Variable::NUMERIC:
                $number = $var->toNumeric();
                if (is_int($number)) {
                    $this->castFrom(Variable::TYPE_INTEGER, $var);
                } else {
                    $this->castFrom(Variable::TYPE_FLOAT, $var);
                }
            case Variable::TYPE_INTEGER:
                $this->integer = $var->toInt();
                break;
            case Variable::TYPE_FLOAT:
                $this->float = $var->toFloat();
                break;
            case Variable::TYPE_BOOLEAN:
                $this->bool = $var->toBool();
                break;
            case Variable::TYPE_STRING:
                $this->string = $var->toString();
                break;
            default:
                throw new \LogicException("Unsupported cast type $type");
        }
    }

    public function copyFrom(self $var): void {
        if ($this->type === self::TYPE_INDIRECT) {
            // always assign to the indirection
            $this->indirect->copyFrom($var);
            return;
        }
        while ($var->type === self::TYPE_INDIRECT) {
            // destroy the indirection
            $var = $var->indirect;
        }
        switch ($var->type) {
            case self::TYPE_STRING:
                $this->string($var->string);
                break;
            case self::TYPE_INTEGER:
                $this->int($var->integer);
                break;
            case self::TYPE_FLOAT:
                $this->float($var->float);
                break;
            case self::TYPE_BOOLEAN:
                $this->bool($var->bool);
                break;
            case self::TYPE_OBJECT:
                $this->object($var->object);
                break;
            default:
                var_dump($var);
                throw new \LogicException("Unsupported type copy: {$var->type}");
        }
    }

    public function identicalTo(Variable $other): bool {
        $self = $this->resolveIndirect();
        $other = $other->resolveIndirect();
        if ($self->type !== $other->type) {
            return false;
        }
        return $self->equals($other);
    }

    public function equals(Variable $other): bool {
        $self = $this;
restart:
        $pair = type_pair($self->type, $other->type);
        switch ($pair) {
            case TYPE_PAIR_INTEGER_INTEGER:
                return $self->integer === $other->integer;
            case TYPE_PAIR_FLOAT_FLOAT:
                return $self->float === $other->float;
            case TYPE_PAIR_STRING_STRING:
                return $self->string === $other->string;
            case TYPE_PAIR_OBJECT_OBJECT:
                return $self->object === $other->object;
            case TYPE_PAIR_INTEGER_FLOAT:
                return ((float) $self->integer) === $other->float;
            case TYPE_PAIR_FLOAT_INTEGER:
                return $self->float === ((float) $other->integer);
            default:
                if ($self->type === self::TYPE_INDIRECT) {
                    $self = $self->indrect;
                    goto restart;
                } elseif ($other->type === self::TYPE_INDIRECT) {
                    $other = $other->indirect;
                    goto restart;
                }
        }
        throw new \LogicException("Equals comparison between {$self->type} and {$other->type} not implemented");
    }

    public function compareOp(int $opCode, Variable $left, Variable $right): void {
        $this->reset();
restart:
        switch (type_pair($left->type, $right->type)) {
            case TYPE_PAIR_INTEGER_INTEGER:
                $this->bool($this->_compareOp($opCode, $left->integer, $right->integer));
                break;
            case TYPE_PAIR_INTEGER_FLOAT:
                $this->bool($this->_compareOp($opCode, $left->integer, $right->float));
                break;
            case TYPE_PAIR_FLOAT_INTEGER:
                $this->bool($this->_compareOp($opCode, $left->float, $right->integer));
                break;
            case TYPE_PAIR_FLOAT_FLOAT:
                $this->bool($this->_compareOp($opCode, $left->float, $right->float));
                break;
            case TYPE_PAIR_STRING_STRING:
                $this->bool($this->_compareOp($opCode, $left->string, $right->string));
                break;
            default:
                if ($left->type === self::TYPE_INDIRECT) {
                    $left = $left->indirect;
                    goto restart;
                } elseif ($right->type === self::TYPE_INDIRECT) {
                    $right = $right->indirect;
                    goto restart;
                } else {
                    $this->bool($this->_compareOp($opCode, $left->toNumeric(), $right->toNumeric()));
                }
        }
    }

    private function _compareOp(int $opCode, $left, $right): bool {
        switch ($opCode) {
            case OpCode::TYPE_IDENTICAL:
               return $left === $right;
            case OpCode::TYPE_GREATER:
                return $left > $right;
            case OpCode::TYPE_SMALLER:
               return $left < $right;
            case OpCode::TYPE_GREATER_OR_EQUAL:
                return $left >= $right;
            case OpCode::TYPE_SMALLER_OR_EQUAL:
                return $left <= $right;
            default:
                throw new \LogicException("Non-implemented numeric comparison operation $opCode");
        }
    }

    public function numericOp(int $opCode, Variable $left, Variable $right): void {
        $this->reset();
restart:
        $pair = type_pair($left->type, $right->type);
        if ($pair === TYPE_PAIR_INTEGER_INTEGER) {
            $result = $this->_numericOp($opCode, $left->integer, $right->integer);        
            if (is_int($result)) {
                $this->int($result);
            } else {
                $this->float($result);
            }
        } elseif ($pair === TYPE_PAIR_INTEGER_FLOAT) {
            $this->float($this->_numericOp($opCode, $left->integer, $right->float));
        } elseif ($pair === TYPE_PAIR_FLOAT_INTEGER) {
            $this->float($this->_numericOp($opCode, $left->float, $right->integer));
        } elseif ($pair === TYPE_PAIR_FLOAT_FLOAT) {
            $this->float($this->_numericOp($opCode, $left->float, $right->float));
        } elseif ($left->type === self::TYPE_INDIRECT) {
            $left = $left->indirect;
            goto restart;
        } elseif ($right->type === self::TYPE_INDIRECT) {
            $right = $right->indirect;
            goto restart;
        } else {
            $result = $this->_numericOp($opCode, $left->toNumeric(), $right->toNumeric());
            if (is_int($result)) {
                $this->int($result);
            } else {
                $this->float($result);
            }
        }
    }

    private function _numericOp(int $opCode, $left, $right) {
        switch ($opCode) {
            case OpCode::TYPE_PLUS:
               return $left + $right;
            case OpCode::TYPE_MINUS:
                return $left - $right;
            case OpCode::TYPE_MUL:
                return $left * $right;
            case OpCode::TYPE_DIV:
                return $left / $right;
            default:
                throw new \LogicException("Non-implemented numeric binary operation $opCode");
        }
    }

    public function unaryOp(int $opCode, Variable $expr): void {
        $this->reset();
restart:
        switch ($opCode) {
            case OpCode::TYPE_UNARY_PLUS:
                $this->castFrom(self::NUMERIC, $expr);
                return;
            case OpCode::TYPE_UNARY_MINUS:
                if ($expr->type === Variable::TYPE_INTEGER) {
                    $this->copyFrom($expr);
                    $this->integer *= -1;
                    return;
                } elseif($expr->type === Variable::TYPE_FLOAT) {
                    $this->copyFrom($expr);
                    $this->float *= -1.0;
                    return;
                } else {
                    $this->castFrom(self::NUMERIC($expr));
                    goto restart;
                }
                break;
        }
        throw new \LogicException("UnaryOp $opCode not implemented for type $expr->type");
    }
}

const TYPE_PAIR_INTEGER_INTEGER = (Variable::TYPE_INTEGER << 8) | Variable::TYPE_INTEGER;
const TYPE_PAIR_FLOAT_INTEGER = (Variable::TYPE_FLOAT << 8) | Variable::TYPE_INTEGER;
const TYPE_PAIR_INTEGER_FLOAT = (Variable::TYPE_INTEGER << 8) | Variable::TYPE_FLOAT;
const TYPE_PAIR_FLOAT_FLOAT = (Variable::TYPE_FLOAT << 8) | Variable::TYPE_FLOAT;
const TYPE_PAIR_STRING_STRING = (Variable::TYPE_STRING << 8) | Variable::TYPE_STRING;
const TYPE_PAIR_OBJECT_OBJECT = (Variable::TYPE_OBJECT << 8) | Variable::TYPE_OBJECT;

function type_pair(int $left, int $right): int {
    return ($left << 8) | $right;
}