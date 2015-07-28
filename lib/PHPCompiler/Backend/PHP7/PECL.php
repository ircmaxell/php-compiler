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
use PHPTypes\Type;

class PECL extends AbstractBackend {

    private $state;

    protected function initState() {
        $this->state = new \StdClass;
        $this->state->name = "compiled_" . mt_rand();
        $this->state->uppername = strtoupper($this->state->name);
        $this->state->functions = [];
        $this->state->functionHeaders = [];
        $this->state->functionEntry = [];
        $this->state->argInfo = [];
        $this->state->scope = new \SplObjectStorage;
        $this->state->labels = new \SplObjectStorage;
        $this->state->seen = new \SplObjectStorage;
        $this->state->stringConstants = [];
        $this->state->decl = [];
        $this->state->freeFlags = [];
    }

    protected function compileFunction(Op\Stmt\Function_ $func) {
        $this->state->freeFlags = [];
        $this->state->functionHeaders[] = "PHP_FUNCTION({$func->name->value});";
        $this->state->functionEntry[] = "PHP_FE({$func->name->value}, arginfo_{$func->name->value})";
        $this->state->argInfo[] = $this->generateArgInfo($func);
        $code = "PHP_FUNCTION({$func->name->value}) {\n";
        $zpp = "";
        $required = 0;
        $optional = false;
        $total = 0;
        foreach ($func->params as $param) {
            $total++;
            if (!$optional && !$param->defaultVar) {
                $required++;
            } elseif (!$optional) {
                $zpp .= "\t\tZ_PARAM_OPTIONAL\n";
                $optional = true;
            }
            $zpp .= "\t\t" . $this->compileZppDecl($param) . "\n";
            $code .= $this->clearDecl("\t");
        }
        $code .= "\tZEND_PARSE_PARAMETERS_START($required, $total)\n$zpp\tZEND_PARSE_PARAMETERS_END();\n";
        $body = $this->compileBody($func->stmts, "\t");
        $code .= $this->clearDecl("\t");
        $code .= $body;

        $code .= "\t\0free-all-vars\0\n}\n";
        $this->state->functions[] = $this->handleFrees($code);
    }

    protected function handleFrees($code) {
        $freeFlags = $this->state->freeFlags;
        $this->state->freeFlags = [];
        foreach (array_keys($freeFlags) as $var) {
            $tmp = $freeFlags[$var];
            unset($freeFlags[$var]);
            $code = str_replace("\0free-$var\0", $this->makeFreeBlock($freeFlags), $code);
            $freeFlags[$var] = $tmp;
        }
        $code = str_replace("\0free-all-vars\0", $this->makeFreeBlock($freeFlags), $code);
        return $code;
    }

    protected function makeFreeBlock(array $freeFlags) {
        $freeBlock = [];
        foreach ($freeFlags as $name => $type) {
            switch ($type) {
                case 'zend_string*':
                    $freeBlock[] = "if (free_{$name}) { zend_string_release({$name}); }";
                    break;
                default:
                    throw new \LogicException("Unknown free type: $type");
            }
        }
        return implode("\n\t", $freeBlock);
    }

    protected function finish() {
        return [
            "php_{$this->state->name}.h" => $this->compileFile('module.h'),
            "{$this->state->name}.c"     => $this->compileFile('module.c'),
            "config.m4"                  => $this->compileFile('module.m4'),
        ];
    }

    protected function compileFile($FILENAME) {
        extract((array) $this->state, EXTR_SKIP);
        ob_start();
        include __DIR__ . "/templates/{$FILENAME}.php";
        return ob_get_clean();
    }

    protected function clearDecl($indent) {
        $decl = implode("\n$indent", $this->state->decl);
        $this->state->decl = [];
        return $decl ? $indent . $decl . "\n" : "";
    }

    protected function compileZppDecl($param) {
        $name = $this->getVarName($param->result);
        switch ($this->mapToCType($param->result->type)) {
            case 'HashTable*':
                return "Z_PARAM_ARRAY_HT($name)";
            case 'zend_string*':
                return "Z_PARAM_STR($name)";
            case 'long':
                return "Z_PARAM_LONG($name)";
            case 'double':
                return "Z_PARAM_DOUBLE($name)";
            case 'zend_bool':
                return "Z_PARAM_BOOL($name)";
        }
        throw new \RuntimeException("Unknown ZPP type found for $param->result->type");
    }

    protected function generateArgInfo(Op\Stmt\Function_ $func) {
        $byRef = $func->byRef ? "1" : "0";
        $required = 0;
        foreach ($func->params as $param) {
            if ($param->defaultVar) {
                break;
            }
            $required++;
        }
        $code = "ZEND_BEGIN_ARG_INFO_EX(arginfo_{$func->name->value}, 0, $byRef, $required)\n";
        foreach ($func->params as $param) {
            $byRef = $param->byRef ? "1" : "0";
            $code .= "\tZEND_ARG_INFO($byRef, {$param->name->value})\n";
        }
        $code .= "ZEND_END_ARG_INFO";
        return $code;
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
                    $result .= $this->compileBody($op->if, $indent . "\t");
                    $result .= $indent . "} else {\n";
                    $result .= $this->compileBody($op->else, $indent . "\t");
                    $result .= $indent . "}\n";
                    break;
                case 'Terminal_Echo':
                    $result .= $this->compilePrintStatement($op, $indent) . ";\n";
                    break;
                case 'Terminal_Return':
                    $return = "\0free-all-vars\0\n{$indent}return;";
                    if ($op->expr) {
                        $cType = $this->mapToCType($op->expr->type);
                        $var = $this->getVarName($op->expr);
                        switch ($cType) {
                            case 'double':
                                $return = "RETURN_DOUBLE($var);";
                                break;
                            case 'long':
                                $return = "RETURN_LONG($var);";
                                break;
                            case 'zend_bool':
                                $return = "RETURN_BOOL($var);";
                                break;
                            case 'zend_string*':
                                $return = "\0free-$var\0\n{$indent}RETURN_STR($var);";
                                break;
                            default:
                                throw new \LogicException("Unknown C Return Type For $cType");
                        }
                    }
                    $result .= "{$indent}{$return}\n";
                    return $result;
                default:
                    throw new \RuntimeException("Unknown op compilation attempt: " . $op->getType());
            }
        }
        return $result;
    }

    protected function compileExpr(Op\Expr $op, $indent) {
        $phi = '';
        foreach ($op->result->usages as $usage) {
            if ($usage instanceof Op\Phi) {
                $phi .= $indent . $this->getVarName($usage->result) . " = " . $this->getVarName($op->result) . ";\n";
            }
        }
        $result = '';
        switch ($op->getType()) {
            case 'Expr_ArrayDimFetch':
                $var = $this->getVarName($op->var);
                $dim = $this->getVarName($op->dim);
                $result = $this->getVarName($op->result);
                switch ($this->mapToCType($op->var->type)) {
                    case 'zend_string*':
                        assert($this->mapToCType($op->dim->type) === 'long');
                        $safety = $indent . "if ($dim < 0 || $dim >= ZSTR_LEN({$var})) {\n";
                        $safety .= $indent . "\t$result = ZSTR_EMPTY_ALLOC();\n";
                        $safety .= $indent . "\tzend_error(E_NOTICE, \"Uninitialized string offset: %pd\", $dim);\n";
                        $safety .= $indent . "} else if (CG(one_char_string)[(unsigned char) ZSTR_VAL($var)[$dim]]) {\n";
                        $safety .= $indent . "\t$result = CG(one_char_string)[(unsigned char) ZSTR_VAL($var)[$dim]];\n";
                        $safety .= $indent . "} else {\n";
                        $safety .= $indent . "\t$result = zend_string_init(ZSTR_VAL($var) + $dim, 1, 0);\n";
                        $safety .= $indent . "\tfree_{$result} = 1;\n";
                        $safety .= $indent . "}\n$phi";
                        return $safety;
                    default:
                        throw new \LogicException("Unknown array dim fetch type {$op->var->type}");
                }
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
                throw new \LogicException("TODO");
                break;
            case 'Expr_BinaryOp_Concat':
                $result = $this->getVarName($op->left) . " . " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Div':
                $result = $this->getVarName($op->left) . " / " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Equal':
                throw new \LogicException("TODO");
                break;
            case 'Expr_BinaryOp_Greater':
                $result = $this->getVarName($op->left) . " > " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_GreaterOrEqual':
                $result = $this->getVarName($op->left) . " >= " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Identical':
                throw new \LogicException("TODO");
                break;
            case 'Expr_BinaryOp_LogicalXor':
                throw new \LogicException("TODO");
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
                throw new \LogicException("TODO");
                break;
            case 'Expr_BinaryOp_NotIdentical':
                throw new \LogicException("TODO");
                break;
            case 'Expr_BinaryOp_Plus':
                $result = $this->getVarName($op->left) . " + " . $this->getVarName($op->right);
                break;
            case 'Expr_BinaryOp_Pow':
                throw new \LogicException("TODO");
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
                throw new \LogicException("TODO");
                break;
            case 'Expr_Print':
                $result = "1;\n" . $this->compilePrintStatement($op, $indent);
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

    protected function compilePrintStatement(Op $op, $indent) {
        $name = $this->getVarName($op->expr);
        switch ($this->mapToCType($op->expr->type)) {
            case 'long':
                return $indent . "php_printf(\"%ld\", {$name})";
            case 'double':
                return $indent . "php_printf(\"%.*G\", (int) EG(precision), {$name})";
            case 'bool':
                return $indent . "php_printf(\"%s\", ({$name}) ? \"1\" : \"\"";
            case 'zend_string*':
                return $indent . "PHPWRITE(ZSTR_VAL({$name}), ZSTR_LEN({$name}))";
            default:
                throw new \LogicException("Unknown type found for print statement: {$op->expr->type}");
        }
    }

    protected function getLabel(Block $block) {
        if (!$this->state->labels->contains($block)) {
            $this->state->labels[$block] = count($this->state->labels) + 1;
        }
        return 'l' . $this->state->labels[$block];
    }

    protected function getVarName(Operand $var) {
        if ($var instanceof Operand\Literal) {
            switch ($this->mapToCType($var->type)) {
                case 'zend_bool':
                case 'long':
                    return (int) $var->value;
                case 'double':
                    // TODO: make this locale independent
                    return sprintf("%d", $var->value);
                case 'zend_string*':
                    if (!isset($this->state->stringConstants[$var->value])) {
                        $this->state->stringConstants[$var->value] = new \StdClass;
                        $this->state->stringConstants[$var->value]->value = $var->value;
                        $this->state->stringConstants[$var->value]->idx = count($this->state->stringConstants) - 1;
                    }
                    return $this->state->uppername . '_G(string_constants)[' . $this->state->stringConstants[$var->value]->idx . ']';
                default:
                    throw new \RuntimeException("Unknown type provided for literal: {$var->type}");
            }
        } elseif (!$this->state->scope->contains($var)) {
            $this->state->scope[$var] = count($this->state->scope) + 1;
            $varName = "v" . $this->state->scope[$var];
            $type = $this->mapToCType($var->type);
            $this->state->decl[] = "$type $varName;";
            switch ($type) {
                case 'zend_string*':
                case 'HashTable*':
                    $this->state->decl[] = "zend_bool free_{$varName} = 0;";
                    $this->state->freeFlags[$varName] = $type;
            }
            return $varName;
        }
        return 'v' . $this->state->scope[$var];
    }

    protected function mapToCType($type) {
        switch ((string) $type) {
            case 'array':
                return 'HashTable*';
            case 'bool':
                return 'zend_bool';
            case 'float':
                return 'double';
            case 'int':
                return 'long';
            case 'string':
                return 'zend_string*';
            case 'mixed':
                return 'zval*';
        }
        var_dump($type);
        throw new \RuntimeException("Unknown C Type Encountered: $type");
    }
}