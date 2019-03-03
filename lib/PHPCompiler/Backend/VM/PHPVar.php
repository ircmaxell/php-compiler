<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

use PHPTypes\Type;

class PHPVar {

    public int $type = Type::TYPE_UNKNOWN;

    public string $string;
    public int $integer;
    public bool $bool;
    public ObjectEntry $object;
    public array $array;

    public function __construct(int $type = Type::TYPE_UNKNOWN) {
        $this->type = $type;
    }

    public function toInt(): int {
        if ($this->type === Type::TYPE_LONG) {
            return $this->integer;
        }
        var_dump($this);
    }

    public function toBool(): bool {
        if ($this->type === Type::TYPE_BOOLEAN) {
            return $this->bool;
        } elseif ($this->type === Type::TYPE_LONG) {
            return $this->integer !== 0;
        }
        var_dump($this);
        // TODO: convert other types
    }

    public function toString(): string {
        switch ($this->type) {
            case Type::TYPE_STRING:
                return $this->string;
            case Type::TYPE_LONG:
                return (string) $this->integer;
        }
        // error
        var_dump($this);
        // TODO: Convert to string
    }

    public function copyFrom(PHPVar $var): void {
        unset($this->string);
        unset($this->integer);
        unset($this->bool);
        unset($this->object);
        $this->type = $var->type;
        switch ($var->type) {
            case Type::TYPE_STRING:
                $this->string = $var->string;
                break;
            case Type::TYPE_LONG:
                $this->integer = $var->integer;
                break;
            case Type::TYPE_BOOLEAN:
                $this->bool = $var->bool;
                break;
            case Type::TYPE_OBJECT:
                $this->object = $var->object;
                break;
            default:
                var_dump($var->type);
                throw new \LogicException("Unsupported type copy");
        }
    }
}