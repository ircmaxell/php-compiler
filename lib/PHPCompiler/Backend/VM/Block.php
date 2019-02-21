<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

use PHPCfg\Block as CfgBlock;

use PHPCfg\Operand;

class Block { 

    /**
     * @var OpCode[] $opCodes
     */
    public array $opCodes = [];

    public int $nOpCodes = 0;

    public CfgBlock $orig;

    private \SplObjectStorage $scope;

    private \SplObjectStorage $phi;

    /** 
     * @var PHPVar[] $constants
     */
    public array $constants = [];

    /**
     * @var function(Scope):void
     */
    public $handler = null;

    public function __construct(CfgBlock $block) {
        $this->orig = $block;
        $this->scope = new \SplObjectStorage;
        $this->phi = new \SplObjectStorage;
        foreach ($block->phi as $phi) {
            $this->phi[$phi->result] = $phi;
        }
    }

    public function getVarSlot(Operand $operand): int {
        if (!$this->scope->contains($operand)) {
            $this->scope[$operand] = $this->scope->count();
        }
        return $this->scope[$operand];
    }

    public function registerConstant(Operand $operand, PHPVar $const): int {
        $slot = $this->getVarSlot($operand);
        $this->constants[$slot] = $const;
        return $slot;
    }

    public function addOpCode(OpCode ...$ops): void {
        foreach ($ops as $op) {
            $this->nOpCodes++;
            $this->opCodes[] = $op;
        }
    }

    public function findSlot(Operand $op, Frame $frame): ?PHPVar {
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
            } elseif ($this->phi->contains($op)) {
                if (is_null($frame)) {
                    throw new \LogicException("Phi var with no parent frame, illegal");
                }
                $phi = $this->phi[$op];
                $found = false;
                foreach ($phi->vars as $var) {
                    $temp = $frame->block->findSlot($var, $frame);
                    if ($temp) {
                        $scope[$pos] = $temp;
                        $found = true;
                    }
                }
                if (!$found) {
                    throw new \LogicException("Could not resolve Phi");
                }
            } else { 
                if (!is_null($frame)) {
                    $parent = $frame->block->findSlot($op, $frame);
                    if (!is_null($parent)) {
                        $scope[$pos] = $parent;
                        continue;
                    }
                }
                $scope[$pos] = new PHPVar;
            }
        }

        return new Frame($this, $frame, ...$scope);
    }


}