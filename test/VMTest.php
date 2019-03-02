<?php

namespace PHPCompiler;

use PHPUnit\Framework\TestCase;
use PHPCompiler\Backend\VM\Runtime;

class VMTest extends BaseTest {

    public function setUp(): void {
        $this->BIN = realpath(__DIR__ . '/../bin/vm.php');
    }

}