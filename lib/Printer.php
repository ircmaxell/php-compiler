<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCfg\Operand;


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
            $return .= $this->renderArg($op->arg1, $block);
            $return .= ', ' . $this->renderArg($op->arg2, $block);
            $return .= ', ' . $this->renderArg($op->arg3, $block);
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

    private function renderArg(?int $arg, Block $block): string {
        if (is_null($arg)) {
            return 'null';
        }
        $operand = $block->getOperand($arg);
        if ($operand instanceof Operand\Literal) {
            return 'LITERAL(' . var_export($operand->value, true) . ')';
        }
        return '$' . $arg;
    }
    

}