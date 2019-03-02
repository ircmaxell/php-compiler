<?php

namespace PHPCompiler;

use PHPUnit\Framework\TestCase;
use PHPCompiler\Backend\VM\Runtime;

class JITTest extends BaseTest {

    public function setUp(): void {
        $this->BIN = realpath(__DIR__ . '/../bin/jit.php');
    }

}