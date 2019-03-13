<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\NativeType;

use PHPCompiler\VM\Variable;

final class NativeArray {
    private \SplFixedArray $data;
    private int $powerOf2;
    private int $mask;

    private function __construct(int $powerOf2) {
        $this->powerOf2 = $powerOf2;
        $size = 1 << $this->powerOf2;
        $this->mask = $size - 1;
        $this->data = new \SplFixedArray($size);
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
        return 1 << $this->powerOf2;
    }

    public function grow(): void {
        $this->powerOf2 = 1 << $this->powerOf2;
        $size = 1 << $this->powerOf2;
        $this->mask = $size - 1;
        $this->data->setSize($size);
    }
    

}