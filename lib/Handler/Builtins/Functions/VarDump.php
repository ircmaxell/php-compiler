<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Handler\Builtins\Functions;

use PHPCompiler\Handler\Builtins\Functions;
use PHPCompiler\Frame;
use PHPCompiler\VM\Variable as VMVariable;
use PHPCompiler\VM\ClassEntry;
use PHPTypes\Type;

class VarDump extends Functions {

    public function getName(): string {
        return 'var_dump';
    }

    public function execute(Frame $frame): void {
        foreach ($frame->calledArgs as $arg) {
            $this->var_dump($arg, 1);
        }
    }

    private function var_dump(VMVariable $var, int $level) {
        if ($level > 1) {
            echo str_repeat(' ', $level - 1);
        }
restart:
        switch ($var->type) {
            case VMVariable::TYPE_INTEGER:
                printf("int(%d)\n", $var->toInt());
                break;
            case VMVariable::TYPE_FLOAT:
                printf("float(%G)\n", $var->toFloat());
                break;
            case VMVariable::TYPE_STRING:
                printf("string(%d) \"%s\"\n", strlen($var->toString()), $var->toString());
                break;
            case VMVariable::TYPE_BOOLEAN:
                printf("bool(%s)\n", $var->toBool() ? 'true' : 'false');
                break;
            case VMVariable::TYPE_OBJECT:
                $props = $var->object->getProperties(ClassEntry::PROP_PURPOSE_DEBUG);
                printf("object(%s)#%d (%d) {\n", $var->toObject()->class->name, $var->toObject()->id, count($props));
                foreach ($props as $key => $prop) {
                    $this->var_dump_object_property($key, $prop, $level);
                }
                if ($level > 1) {
                    echo str_repeat(' ', $level - 1);
                }
                echo "}\n";
                break;
            case VMVariable::TYPE_INDIRECT:
                $var = $var->resolveIndirect();
                goto restart;
            default:
                throw new \LogicException("var_dump not implemented for type");
        }
    }

    private function var_dump_object_property(string $key, VMVariable $prop, int $level) {
        echo str_repeat(' ', $level + 1);
        printf("[\"%s\"]=>\n", $key);
        $this->var_dump($prop, $level + 2);
    }

    public function getReturnType(): string {
        return 'void';
    }
    public function getParamTypes(): array {
        return [
            '__value__'
        ];
    }

    public function implement(\gcc_jit_function_ptr $func, \gcc_jit_param_ptr ...$params): void {
        $block = \gcc_jit_function_new_block($func, 'main');
        //todo figure out how to compile var_dump
        \gcc_jit_block_end_with_void_return($block, $this->jitContext->location());
    }

}