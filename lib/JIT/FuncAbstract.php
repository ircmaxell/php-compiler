<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

abstract class FuncAbstract implements Func {
    private static int $argConvertCounter = 0;

    public string $name;
    public \gcc_jit_function_ptr $func;
    public \gcc_jit_type_ptr $returnType;
    public array $params;
    public $defineCallback = null;
    protected Context $context;

    public function __construct(
        Context $context,
        string $name, 
        \gcc_jit_function_ptr $func, 
        \gcc_jit_type_ptr $returnType, 
        \gcc_jit_param_ptr ... $params
    ) {
        $this->context = $context;
        $this->name = $name;
        $this->func = $func;
        $this->returnType = $returnType;
        $this->params = $params;
    }

    abstract public function call(\gcc_jit_rvalue_ptr ...$args): \gcc_jit_rvalue_ptr;

    protected function buildSignature(\gcc_jit_rvalue_ptr ... $params): string {
        $sig = [];
        foreach ($params as $param) {
            $sig[] = $this->context->getStringFromType(gcc_jit_rvalue_get_type($param));
        }
        $result = $this->context->getStringFromType($this->returnType) . '(' . implode(',', $sig) . ')';
        return $result;
    }

    protected function convertArgForParam(\gcc_jit_function_ptr $func, \gcc_jit_block_ptr $block, \gcc_jit_type_ptr $type, \gcc_jit_rvalue_ptr $rvalue): \gcc_jit_rvalue_ptr {
        $to = $this->context->getStringFromType($type);
        $from = $this->context->getStringFromType(\gcc_jit_rvalue_get_type($rvalue));
        if ($from === $to) {
            return $rvalue;
        }
        if ($to === '__value__') {
            $local = \gcc_jit_function_new_local(
                $func, 
                $this->context->location(), 
                $this->context->getTypeFromString($to),
                'converted_' . (self::$argConvertCounter++)
            );
            switch ($from) {
                case 'long long':
                    $this->context->type->value->writeLong($block, $local, $rvalue);
                    break;
                case 'double':
                    $this->context->type->value->writeFloat($block, $local, $rvalue);
                    break;
                case '__string__*':
                    $this->context->type->value->writeString($block, $local, $rvalue);
                    break;
                default:
                    throw new \LogicException('Unsupported native type: ' . $from);
            }
            return $local->asRValue();
        }
        throw new \LogicException("Type pair not supported: $from -> $to");
    }

}