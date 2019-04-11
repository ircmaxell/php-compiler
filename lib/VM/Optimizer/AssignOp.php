<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\VM\Optimizer;

use PHPCompiler\Block;
use PHPCompiler\OpCode;
use PHPCompiler\VM\Optimizer;

class AssignOp extends Optimizer
{
    const CANDIDATE_OPS = [
        OpCode::TYPE_CONCAT,
    ];

    public function optimize(Block $block, ?\SplObjectStorage $seen = null): void
    {
        $seen = $seen ?? new \SplObjectStorage();
        if ($seen->contains($block)) {
            return;
        }
        $seen->attach($block);
        $prior = null;
        $toRemove = [];
        foreach ($block->opCodes as $key => $op) {
            if ($op->type === OpCode::TYPE_ASSIGN && null !== $prior && in_array($prior->type, self::CANDIDATE_OPS, true)) {
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
            if (null !== $op->block1) {
                $this->optimize($op->block1, $seen);
            }
            if (null !== $op->block2) {
                $this->optimize($op->block2, $seen);
            }
        }

        if (! empty($toRemove)) {
            foreach ($toRemove as $key) {
                unset($block->opCodes[$key]);
            }
            $block->opCodes = array_values($block->opCodes);
        }
    }
}
