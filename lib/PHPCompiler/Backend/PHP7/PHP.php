<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\PHP7;

use PHPCfg\Block;
use PHPCfg\Op;
use PHPCfg\Operand;
use PHPCompiler\AbstractBackend;

class PHP extends AbstractBackend {

    private $state;

    protected function initState() {
        $this->state = new \StdClass;
        $this->state->functions = [];
        $this->state->scope = new \SplObjectStorage;
        $this->state->labels = new \SplObjectStorage;
        $this->state->seen = new \SplObjectStorage;
    }

    protected function compileFunction(Op\Stmt\Function_ $func) {
        $signature = 'function ' . ($func->byRef ? '&' : '') . $func->name->value . '(';
        $sep = '';
        foreach ($func->params as $param) {
            $signature .= $sep;
            $sep = ', ';
            $signature .= $param->result->type;
            $signature .= $param->byRef ? ' &' : ' ';
            $signature .= $this->getVarName($param->result);
        }
        $signature .= ') : ' . $func->returnType->value;
        $code = $this->compileBody($func->stmts, '    ');
        $this->state->functions[] = $signature . " {\n" . $code . "\n}";
    }

    protected function finish() {
        return "<?php\n" . implode("\n", $this->state->functions);
    }

    protected function compileBody(Block $block, $indent) {
        if ($this->state->seen->contains($block)) {
            return '';
        }
        $result = '';
        $this->state->seen->attach($block);
        foreach ($block->phi as $phi) {
            foreach ($phi->vars as $var) {
                if ($var instanceof Operand\Literal) {
                    $result .= $indent . $this->getVarName($phi->result) . " = " . $this->getVarName($var) . ";\n";
                }
            }
        }
        // This comes after constant phi assignment, since we can possibly re-enter
        $result .= $this->getLabel($block) . ":\n";
        foreach ($block->children as $op) {
            if ($op instanceof Op\Expr) {
                $result .= $this->compileExpr($op, $indent);
                continue;
            }
            switch ($op->getType()) {
                case 'Stmt_Jump':
                    if ($this->state->seen->contains($op->target)) {
                        // Only issue the goto if it's not the next statement
                        $result .= $indent . "goto " . $this->getLabel($op->target) . ";\n";
                    }
                    $result .= $this->compileBody($op->target, $indent);
                    break;
                case 'Stmt_JumpIf':
                    $result .= $indent . "if (" . $this->getVarName($op->cond) . ") {\n";
                    $result .= $this->compileBody($op->if, $indent . '    ');
                    $result .= $indent . "} else {\n";
                    $result .= $this->compileBody($op->else, $indent . '    ');
                    $result .= $indent . "}\n";
                    break;
                case 'Terminal_Return':
                    $result .= $indent . 'return';
                    if ($op->expr) {
                        $result .= ' ' . $this->getVarName($op->expr);
                    }
                    $result .= "\n";
                    return $result;
                default:
                    throw new \RuntimeException("Unknown op compilation attempt: " . $op->getType());
            }
        }
        return $result;
    }

    protected function compileExpr(Op\Expr $op, $indent) {
        $phi = '';
        $result = '';
        switch ($op->getType()) {
            case 'Expr_ArrayDimFetch':
                $result = $this->getVarName($op->var) . "[" . $this->getVarName($op->dim) . "]";
                break;
            case 'Expr_Assign':
                $result = $this->getVarName($op->var) . ' = ' . $this->getVarName($op->expr);
                foreach ($op->var->usages as $usage) {
                    if ($usage instanceof Op\Phi) {
                        $phi .= $indent . $this->getVarName($usage->result) . " = " . $this->getVarName($op->var) . ";\n";
                    }
                }
                break;
            case 'Expr_BinaryOp_BitwiseAnd':
                $result = $this->getVarName($op->left) . " & " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_BitwiseOr':
                $result = $this->getVarName($op->left) . " | " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_BitwiseXor':
                $result = $this->getVarName($op->left) . " ^ " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Coalesce':
                $result = $this->getVarName($op->left) . " ?? " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Concat':
                $result = $this->getVarName($op->left) . " . " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Div':
                $result = $this->getVarName($op->left) . " / " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Equal':
                $result = $this->getVarName($op->left) . " == " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Greater':
                $result = $this->getVarName($op->left) . " > " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_GreaterOrEqual':
                $result = $this->getVarName($op->left) . " >= " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Identical':
                $result = $this->getVarName($op->left) . " === " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_LogicalXor':
                $result = $this->getVarName($op->left) . " XOR " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Minus':
                $result = $this->getVarName($op->left) . " - " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Mod':
                $result = $this->getVarName($op->left) . " % " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Mul':
                $result = $this->getVarName($op->left) . " * " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_NotEqual':
                $result = $this->getVarName($op->left) . " != " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_NotIdentical':
                $result = $this->getVarName($op->left) . " !== " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Plus':
                $result = $this->getVarName($op->left) . " + " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Pow':
                $result = $this->getVarName($op->left) . " ** " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_ShiftLeft':
                $result = $this->getVarName($op->left) . " << " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_ShiftRight':
                $result = $this->getVarName($op->left) . " >> " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Smaller':
                $result = $this->getVarName($op->left) . " < " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_SmallerOrEqual':
                $result = $this->getVarName($op->left) . " <= " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Spaceship':
                $result = $this->getVarName($op->left) . " <=> " . $this->getVarName($op->right);
                break;
            case 'Expr_Cast_Array':
                $result = "(array) " . $this->getVarName($op->expr);
                break;
            case 'Expr_Cast_Bool':
                $result = "(bool) " . $this->getVarName($op->expr);
                break;
            case 'Expr_Cast_Double':
                $result = "(float) " . $this->getVarName($op->expr);
                break;
            case 'Expr_Cast_Int':
                $result = "(int) " . $this->getVarName($op->expr);
                break;
            case 'Expr_Cast_Object':
                $result = "(object) " . $this->getVarName($op->expr);
                break;
            case 'Expr_Cast_String':
                $result = "(string) " . $this->getVarName($op->expr);
                break;
            case 'Expr_Cast_Unset':
                $result = "(unset) " . $this->getVarName($op->expr);
                break;
            default:
                throw new \RuntimeException("Unknown expression found: " . $op->getType());
        }
        foreach ($op->result->usages as $usage) {
            if ($usage instanceof Op\Phi) {
                $phi .= $indent . $this->getVarName($usage->result) . " = " . $this->getVarName($op->result) . ";\n";
            }
        }
        if (count($op->result->usages) === 0) {
            return $indent . $result . ";\n" . $phi;
        }
        return $indent . $this->getVarName($op->result) . " = " . $result . ";\n" . $phi;

    }

    protected function getLabel(Block $block) {
        if (!$this->state->labels->contains($block)) {
            $this->state->labels[$block] = count($this->state->labels) + 1;
        }
        return 'l' . $this->state->labels[$block];
    }

    protected function getVarName(Operand $var) {
        if ($var instanceof Operand\Literal) {
            return $var->value;
        } elseif (!$this->state->scope->contains($var)) {
            $this->state->scope[$var] = count($this->state->scope) + 1;
        }
        return '$v' . $this->state->scope[$var];
    }
}