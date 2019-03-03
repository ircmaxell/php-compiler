<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\Builtins;

use PHPCompiler\Backend\VM\Block;
use PHPCompiler\Backend\VM\ClassEntry;
use PHPCompiler\Backend\VM\Context;
use PHPCompiler\Backend\VM\Frame;
use PHPCompiler\Backend\VM\Handler;
use PHPCompiler\Backend\VM\PHPVar;
use PHPTypes\Type;

class Basic {

    public function register(Context $context) {
        $this->registerFunction($context, 'var_dump', function(Frame $frame) {
            foreach ($frame->calledArgs as $arg) {
                self::var_dump($arg, 1);
            }
        });
        $this->registerFunction($context, 'strlen', function(Frame $frame) {
            $var = $frame->calledArgs[0];
            if (!is_null($frame->returnVar)) {
                $frame->returnVar->type = Type::TYPE_LONG;
                $frame->returnVar->integer = strlen($var->toString());
            }
        });
        $context->classes['stdclass'] = new ClassEntry('stdClass');
    }

    private function registerFunction(Context $context, string $name, callable $cb) {
        $context->functions[$name] = new Block(null);
        $context->functions[$name]->handler = new Handler($cb, null);
    }

    private static function var_dump(PHPVar $var, int $level) {
        if ($level > 1) {
            printf('%*c', $level - 1, ' ');
        }
        switch ($var->type) {
            case Type::TYPE_LONG:
                printf("int(%d)\n", $var->integer);
                break;
            case Type::TYPE_STRING:
                printf("string(%d) \"%s\"\n", strlen($var->string), $var->string);
                break;
            case Type::TYPE_BOOLEAN:
                printf("bool(%s)\n", $var->bool ? 'true' : 'false');
                break;
            case Type::TYPE_OBJECT:
                $props = $var->object->getProperties(ClassEntry::PROP_PURPOSE_DEBUG);
                printf("object(%s)#%d (%d) {\n", $var->object->class->name, $var->object->id, count($props));
                foreach ($props as $key => $prop) {
                    self::var_dump_object_property($key, $prop, $level);
                }
                if ($level > 1) {
                    printf("%*c", $level - 1, ' ');
                }
                echo "}\n";
                break;
            default:
                throw new \LogicException("var_dump not implemented for type");
        }
    }

    private static function var_dump_object_property(string $key, PHPVar $prop, int $level) {
        printf("\"%s\" =>\n", $key);
        self::var_dump($prop, $level + 2);
    }
}