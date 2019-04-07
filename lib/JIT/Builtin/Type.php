<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;

use PHPLLVM;

class Type extends Builtin {

    public Type\String_ $string;
    protected array $fields;

    public function register(): void {
        $this->string = new Type\String_($this->context, $this->loadType);
        // $this->object = new Type\Object_($this->context, $this->loadType);
        // $this->value = new Type\Value($this->context, $this->loadType);
        // $this->hashtable = new Type\HashTable($this->context, $this->loadType);
        // $this->maskedarray = new Type\MaskedArray($this->context, $this->loadType);
        // $this->nativearray = new Type\NativeArray($this->context, $this->loadType);
        $this->string->register();
        // $this->object->register();
        // $this->value->register();
        // $this->hashtable->register();
        // $this->maskedarray->register();
        // $this->nativearray->register();
    }


}