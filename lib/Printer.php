<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

class Printer {

    private \SplObjectStorage $seen;

    public function print(Block $block): string {
        $this->seen = new \SplObjectStorage;
        return $this->printBlock($block);
    }

    private function printBlock(Block $block): string {
        if ($this->seen->contains($block)) {
            return '';
        }
        $id = $this->seen->count();
        $this->seen[$block] = $id;
        $return = "block_$id:\n";
        $append = '';
        foreach ($block->opCodes as $op) {
            $return .= '  ' . $op->getType() . '(';
            $return .= is_null($op->arg1) ? 'null' : $op->arg1;
            $return .= ', ' . (is_null($op->arg2) ? 'null' : $op->arg2);
            $return .= ', ' . (is_null($op->arg3) ? 'null' : $op->arg3);
            $return .= ')';
            if (!is_null($op->block1)) {
                $append .= $this->printBlock($op->block1);
                $return .= "\n    goto block_" . $this->seen[$op->block1];
            }
            if (!is_null($op->block2)) {
                $append .= $this->printBlock($op->block2);
                $return .= "\n    goto block_" . $this->seen[$op->block2];
            }
            $return .= "\n";
        }
        return $return . "\n" . $append;
    }
    

}