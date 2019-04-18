<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

// used as a property type.
// @phan-suppress-next-line PhanUnreferencedUseNormal
use PHPCfg\Func;
use PHPCfg\Block as CfgBlock;
use PHPCfg\Operand;
use PHPCompiler\VM\Context;
use PHPCompiler\VM\Variable;

class Block { 

    /**
     * @var OpCode[] $opCodes
     */
    public array $opCodes = [];

    public array $blocks = [];

    public int $nOpCodes = 0;

    public ?Func $func = null;

    public ?CfgBlock $orig;

    private \SplObjectStorage $scope;

    /** 
     * @var Variable[] $constants
     */
    public array $constants = [];

    public \SplObjectStorage $args;

    public ?Handler $handler = null;

    public function __construct(?CfgBlock $block) {
        $this->orig = $block;
        $this->scope = new \SplObjectStorage;
        $this->args = new \SplObjectStorage;
    }

    public function getOperand(int $offset): Operand {
        foreach ($this->scope as $operand) {
            if ($this->scope[$operand] === $offset) {
                return $operand;
            }
        }
    }

    public function getVarSlot(Operand $operand, bool $isRead): int {
        if (!$this->scope->contains($operand)) {
            $this->scope[$operand] = $this->scope->count();
            if ($isRead) {
                $this->args[$operand] = $this->scope[$operand];
            }
        }
        return $this->scope[$operand];
    }

    public function registerConstant(Operand $operand, Variable $const): int {
        $slot = $this->getVarSlot($operand, false);
        $this->constants[$slot] = $const;
        return $slot;
    }

    public function addOpCode(OpCode ...$ops): void {
        foreach ($ops as $op) {
            $this->nOpCodes++;
            $this->opCodes[] = $op;
        }
    }

    public function findSlot(Operand $op, Frame $frame): ?Variable {
        if (!$this->scope->contains($op)) {
            // check PHI vars
            if (!is_null($frame->parent)) {
                return $frame->parent->block->findSlot($op, $frame->parent);
            }
            return null;
        }
        $idx = $this->scope[$op];
        return $frame->scope[$idx];
    }

    public function getFrame(Context $context, ?Frame $frame = null): Frame {
        // Todo: build scope
        $scope = [];
        $scopeSize = $this->scope->count();
        foreach ($this->scope as $op) {
            $pos = $this->scope[$op];
            
            if (isset($this->constants[$pos])) {
                $scope[$pos] = $this->constants[$pos];
            } elseif ($this->args->contains($op)) {
                if (is_null($frame)) {
                    throw new \LogicException("Argument var with no parent frame, illegal");
                }
                $found = false;
                $parent = $frame->block->findSlot($op, $frame);
                if (!is_null($parent)) {
                    $scope[$pos] = $parent;
                    $found = true;
                }
                if (!$found) {
                    throw new \LogicException("Could not resolve argument");
                }
            } else { 
                $scope[$pos] = new Variable(Variable::TYPE_NULL);
            }
        }

        $return = new Frame(null, $this, $frame, ...$scope);
        if (!is_null($frame) && !is_null($frame->returnVar)) {
            $return->returnVar = $frame->returnVar;
        }
        return $return;
    }


}
