<?php

namespace PHPCompiler;

use PHPUnit\Framework\TestCase;
use PHPCompiler\Backend\VM\Runtime;

require_once __DIR__ . '/../BaseTest.php';

class MacroTest extends BaseTest {

    protected static string $DIR = __DIR__;

    public function setUp(): void {
        $this->BIN = realpath(__DIR__ . '/../bin/macro_compile.php');
    }

}