<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\VM;

use PHPTypes\Type;

class Variable {
    const TYPE_UNKNOWN = -1;
    const TYPE_NULL = 0;
    const TYPE_INTEGER = 1;
    const TYPE_BOOLEAN = 2;
    const TYPE_STRING = 3;
    const TYPE_OBJECT = 4;
    const TYPE_INDIRECT = 5;

    public int $type = self::TYPE_NULL;

    public string $string;
    public int $integer;
    public bool $bool;
    public ObjectEntry $object;
    public Variable $indirect;

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

    public function toInt(): int {
        switch ($this->type) {
            case self::TYPE_NULL:
                return 0;
            case self::TYPE_INTEGER:
                return $this->integer;
            case self::TYPE_BOOLEAN:
                return $this->bool ? 1 : 0;
            case self::TYPE_STRING:
                return (int) $this->string;
            case self::TYPE_INDIRECT:
                return $this->indirect->toInt();
        }
    }

    public function toBool(): bool {
        switch ($this->type) {
            case self::TYPE_NULL:
                return false;
            case self::TYPE_INTEGER:
                return 0 !== $this->integer;
            case self::TYPE_BOOLEAN:
                return $this->bool;
            case self::TYPE_STRING:
                return '' === $this->string || '0' === $this->string;
            case self::TYPE_INDIRECT:
                return $this->indirect->toBool();
        }
    }

    public function toString(): string {
        switch ($this->type) {
            case self::TYPE_STRING:
                return $this->string;
            case self::TYPE_INTEGER:
                return (string) $this->integer;
            case self::TYPE_BOOLEAN:
                return $this->bool ? '1' : '';
            case self::TYPE_INDIRECT:
                return $this->indirect->toString();
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
        unset($this->string);
        unset($this->integer);
        unset($this->bool);
        unset($this->object);
        unset($this->indirect);

        $this->type = $var->type;
        switch ($var->type) {
            case self::TYPE_STRING:
                $this->string = $var->string;
                break;
            case self::TYPE_INTEGER:
                $this->integer = $var->integer;
                break;
            case self::TYPE_BOOLEAN:
                $this->bool = $var->bool;
                break;
            case self::TYPE_OBJECT:
                $this->object = $var->object;
                break;
            default:
                var_dump($var);
                throw new \LogicException("Unsupported type copy: {$var->type}");
        }
    }
}