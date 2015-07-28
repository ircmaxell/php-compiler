<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCfg\Block;
use PHPCfg\Op;

abstract class AbstractBackend implements Backend {
    
    public function compile(array $blocks) {
        $this->initState();
        foreach ($blocks as $block) {
            $this->compileGlobal($block);
        }
        return $this->finish();
    }

    protected function compileGlobal(Block $block) {
        foreach ($block->children as $child) {
            switch ($child->getType()) {
                case 'Stmt_Function':
                    $this->compileFunction($child);
                    break;
                default:
                    throw new \RuntimeException("Could not compile global operation: " . $child->getType());
            }
        }
    }

    abstract protected function initState();

    abstract protected function compileFunction(Op\Stmt\Function_ $func);

    abstract protected function finish();

}