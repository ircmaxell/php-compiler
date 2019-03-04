<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

abstract class Builtin {
    const LOAD_TYPE_EXPORT = 1;
    const LOAD_TYPE_IMPORT = 2;
    const LOAD_TYPE_EMBED = 3;
    const LOAD_TYPE_STANDALONE = 4;

    protected Context $context;
    protected int $loadType;

    public function __construct(Context $context, int $loadType) {
        $this->context = $context;
        $context->registerBuiltin($this);
        $this->loadType = $loadType;
    }

    public function register(): void {
    }

    public function implement(): void {
    }

    public function initialize(): void {
    }

    public function shutdown(): void {
    }

}