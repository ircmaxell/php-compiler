<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace php;
use SplFixedArray;

final class MaskedArray {
    private SplFixedArray $data;
    private int $size;
    private int $mask;

    private function __construct(int $powerOf2) {
        $this->size = 1 << $powerOf2;
        $this->mask = $this->size - 1;
        $this->data = new SplFixedArray($this->size);
    }

    public static function allocate(int $powerOf2) {
        return new self($powerOf2);
    }

    public function read(int $index) {
        return $this->data[$index & $this->mask];
    }

    public function write(int $index, $value): void {
        $this->data[$index & $this->mask] = $value;
    }

    public function size(): int {
        return $this->size;
    }

    public function grow(): void {
        $this->size *= 2;
        $this->mask = $this->size - 1;
        $this->data->setSize($this->size);
    }
    

}
