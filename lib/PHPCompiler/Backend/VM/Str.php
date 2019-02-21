<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

use PHPTypes\Type;

class Str {
    public string $value = '';
    public int $length = 0;

    private function __construct() {

    }

    public static function fromPrimitive(string $value): self {
        $self = self::allocate(strlen($value));
        $self->value = $value;
        return $self;
    }

    public static function allocate(int $length): self {
        $self = new self;
        $self->length = $length;
        $self->value = str_repeat("\0", $length);
        return $self;
    }

    public static function memcpy(Str $dest, Str $source, int $offset): void {
        for ($i = 0; $i < $source->length; $i++) {
            $dest->value[$i + $offset] = $source->value[$i];
        }
    }
}
