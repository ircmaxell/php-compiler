<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\Optimizer;

use PHPCompiler\Backend\VM\Block;
use PHPCompiler\Backend\VM\OpCode;
use PHPCompiler\Backend\VM\Optimizer;


class AssignOp extends Optimizer {
    const CANDIDATE_OPS = [
        OpCode::TYPE_CONCAT,
    ];

    public function optimize(Block $block, ?\SplObjectStorage $seen = null) {
        $seen = $seen ?? new \SplObjectStorage;
        if ($seen->contains($block)) {
            return;
        }
        $seen->attach($block);
        $prior = null;
        $toRemove = [];
        foreach ($block->opCodes as $key => $op) {
            if ($op->type === OpCode::TYPE_ASSIGN && !\is_null($prior) && in_array($prior->type, self::CANDIDATE_OPS)) {
                // replace
                $binaryOpResult = $block->getOperand($prior->arg1);
                if (count($binaryOpResult->usages) === 1) {
                    // We can safely replace it with an assign op
                    $prior->arg1 = $op->arg2;
                    $assignResult = $block->getOperand($op->arg1);
                    if (empty($assignResult->usages)) {
                        // remove assign as it's dead
                        $toRemove[] = $key;
                    } else {
                        // We still need the assign, since we're using the result
                        $op->arg2 = $op->arg1;
                    }
                }
            }
            $prior = $op;
            if (!is_null($op->block1)) {
                $this->optimize($op->block1, $seen);
            }
            if (!is_null($op->block2)) {
                $this->optimize($op->block2, $seen);
            }
        }

        if (!empty($toRemove)) {
            foreach ($toRemove as $key) {
                unset($block->opCodes[$key]);
            }
            $block->opCodes = array_values($block->opCodes);
        }
    }

}