<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\NativeType;

use PHPCompiler\VM\Variable;

class NativeArray implements \ArrayAccess {
    private \SplFixedArray $data;

    private function __construct(int $size) {
        $this->data = new \SplFixedArray($size);
        for ($i = 0; $i < $size; $i++) {
            $this->data[$i] = 0;
        }
    }

    public static function allocate(int $size) {
        return new self($size);
    }

    public function offsetGet($index) {
        if (!is_int($index)) {
            throw new \LogicException("Only integer indexes are supported by " . __CLASS__);
        }
        return $this->data->offsetGet($index);
    }

    public function offsetSet($index, $value) {
        if (!is_int($index)) {
            throw new \LogicException("Only integer indexes are supported by " . __CLASS__);
        }
        $this->data->offsetSet($index, $value);
    }

    public function offsetExists($offset) {
        throw new \LogicException("Unexpected call to offset exists");
    }

    public function offsetUnset($offset) {
        throw new \LogicException("Unexpected call to offset unset");
    }

    public static function reallocate(self $self, int $newSize): self {
        $orig = $self->data->count();
        $self->data->setSize($newSize);
        return $self;
    }

}