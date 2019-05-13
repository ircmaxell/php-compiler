<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCompiler\Func;
use PHPCompiler\Handler;

use PHPLLVM;
use FFI;

class Result {
    private PHPLLVM\ExecutionEngine $engine;
    private int $loadType;

    public function __construct(PHPLLVM\ExecutionEngine $engine, int $loadType) {
        $this->engine = $engine;
        $this->loadType = $loadType;
        if ($loadType !== Builtin::LOAD_TYPE_IMPORT) {
            // Call the initialization function!
            $cb = $this->getCallable('__init__', 'void(*)()');
            $cb();
        }
    }

    public function __destruct() {
        if ($this->loadType !== Builtin::LOAD_TYPE_IMPORT) {
            // Call the initialization function!
            $cb = $this->getCallable('__shutdown__', 'void(*)()');
            $cb();
        }
    }

    public function getCallable(string $funcName, string $callbackType): callable {
        $address = $this->engine->getFunctionAddress($funcName);
        $code = FFI::new('size_t');
        $code = $address;
        $cb = FFI::new($callbackType);
        FFI::memcpy(
            FFI::addr($cb),
	    // Incorrectly flagged due to https://github.com/phan/phan/issues/2659
	    // @phan-suppress-next-line PhanTypeMismatchArgumentInternal
            FFI::addr($code),
            FFI::sizeof($cb)
        );

	// Phan isn't smart enough to realize that $cb is an address of a callable.
	//@phan-suppress-next-line PhanTypeMismatchReturn
        return $cb;
    }
}
