<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPTypes\Type;
use PHPCompiler\VM\Context;
use PHPCompiler\VM\ClassEntry;
use PHPCompiler\VM\ObjectEntry;
use PHPCompiler\VM\Variable;

class VM {
    const SUCCESS = 1;
    const FAILURE = 2;

    public static function run(Block $block, Context $context): int {
        
        $context->push($block->getFrame($context));
nextframe:
        $frame = $context->pop();

        if (is_null($frame)) {
            return self::SUCCESS;
        }
restart:

        while ($frame->pos < $frame->block->nOpCodes) {
            $op = $frame->block->opCodes[$frame->pos++];
            switch ($op->type) {
                case OpCode::TYPE_ASSIGN:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg3 = $frame->scope[$op->arg3];
                    $arg2->copyFrom($arg3);
                    $arg1->copyFrom($arg3); 
                    break;
                case OpCode::TYPE_ARRAY_DIM_FETCH:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg3 = $frame->scope[$op->arg3];
                    if ($arg2->type === Variable::TYPE_STRING) {
                        $arg1->string($arg2->toString()[$arg3->toInt()]);
                    } else {
                        throw new \LogicException('Illegal offset');
                    }
                    break;
                case OpCode::TYPE_CAST_BOOL:
                    $frame->scope[$op->arg1]->castFrom(Variable::TYPE_BOOLEAN, $frame->scope[$op->arg2]);
                    break;
                case OpCode::TYPE_IDENTICAL:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg3 = $frame->scope[$op->arg3];
                    $arg1->bool($arg2->identicalTo($arg3));
                    break;
                case OpCode::TYPE_EQUAL:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg3 = $frame->scope[$op->arg3];
                    $arg1->bool($arg2->equals($arg3));
                    break;
                case OpCode::TYPE_SMALLER:
                case OpCode::TYPE_GREATER:
                case OpCode::TYPE_SMALLER_OR_EQUAL:
                case OpCode::TYPE_GREATER_OR_EQUAL:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg3 = $frame->scope[$op->arg3];
                    $arg1->compareOp($op->type, $arg2, $arg3);
                    break;
                case OpCode::TYPE_PLUS:
                case OpCode::TYPE_MINUS:
                case OpCode::TYPE_MUL:
                case OpCode::TYPE_DIV:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg3 = $frame->scope[$op->arg3];
                    $arg1->numericOp($op->type, $arg2, $arg3);
                    break;
                case OpCode::TYPE_BITWISE_AND:
                case OpCode::TYPE_BITWISE_OR:
                case OpCode::TYPE_BITWISE_XOR:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg3 = $frame->scope[$op->arg3];
                    $arg1->bitwiseOp($op->type, $arg2, $arg3);
                    break;

                case OpCode::TYPE_UNARY_MINUS:
                case OpCode::TYPE_UNARY_PLUS:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    $arg1->unaryOp($op->type, $arg2);
                    break;
                case OpCode::TYPE_CONCAT:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2]->toString();
                    $arg3 = $frame->scope[$op->arg3]->toString();
                    $arg1->string($arg2 . $arg3);
                    break;
                case OpCode::TYPE_ECHO:
                    echo $frame->scope[$op->arg1]->toString();
                    break;
                case OpCode::TYPE_PRINT:
                    echo $frame->scope[$op->arg2]->toString();
                    $frame->scope[$op->arg1]->int(1);
                    break;
                case OpCode::TYPE_JUMP:
                    $frame = $op->block1->getFrame(
                        $context,
                        $frame 
                    );
                    goto restart;
                case OpCode::TYPE_JUMPIF:
                    $arg1 = $frame->scope[$op->arg1]->toBool();
                    if ($arg1) {
                        $frame = $op->block1->getFrame($context, $frame);
                    } else {
                        $frame = $op->block2->getFrame($context, $frame);
                    }
                    goto restart;
                case OpCode::TYPE_CASE:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2];
                    if ($arg1->equals($arg2)) {
                        $frame = $op->block1->getFrame($context, $frame);
                        goto restart;
                    }
                    break;
                case OpCode::TYPE_CONST_FETCH:
                    $value = null;
                    if (!is_null($op->arg3)) {
                        // try NS constant fetch
                        $value = $context->constantFetch($frame->scope[$op->arg3]->toString()->value);
                    }
                    if (is_null($value)) {
                        $value = $context->constantFetch($frame->scope[$op->arg2]->toString()->value);
                    }
                    if (is_null($value)) {
                        return $this->raise('Unknown constant fetch', $frame);
                    }
                    $frame->scope[$op->arg1]->copyFrom($value);
                    break;
                case OpCode::TYPE_RETURN_VOID:
                    // TODO
                    goto nextframe;
                case OpCode::TYPE_RETURN:
                    $frame->returnVar->copyFrom($frame->scope[$op->arg1]);
                    goto nextframe;
                case OpCode::TYPE_FUNCDEF:
                    $name = $frame->scope[$op->arg1]->toString();
                    $lcname = strtolower($name);
                    if (isset($context->functions[$lcname])) {
                        throw new \LogicException("Duplicate function definition for $lcname()");
                    }
                    $context->declareFunction(new Func\PHP($name, $op->block1));
                    break;
                case OpCode::TYPE_FUNCCALL_INIT:
                    $name = $frame->scope[$op->arg1]->toString();
                    $lcname = strtolower($name);
                    if (!isset($context->functions[$lcname])) {
                        throw new \LogicException("Call to undefined function $lcname()");
                    }
                    $frame->call = $context->functions[$lcname];
                    $frame->callArgs = [];
                    break;
                case OpCode::TYPE_ARG_SEND:
                    $frame->callArgs[] = $frame->scope[$op->arg1];
                    break;
                case OpCode::TYPE_FUNCCALL_EXEC_RETURN:
                case OpCode::TYPE_FUNCCALL_EXEC_NORETURN:
                    if (is_null($frame->call)) {
                        // Used for null constructors, etc
                        break;
                    }
                    $new = $frame->call->getFrame($context);
                    if ($op->type === OpCode::TYPE_FUNCCALL_EXEC_RETURN) {
                        $new->returnVar = $frame->scope[$op->arg1];
                    }
                    $new->calledArgs = $frame->callArgs;
                    if ($new->hasHandler()) {
                        $new->handler->execute($new);
                        break;
                    }
                    $context->push($frame);
                    $frame = $new;
                    goto restart;
                case OpCode::TYPE_ARG_RECV:
                    // Todo: do type checks and transformations
                    $arg1 = $frame->scope[$op->arg1];
                    $arg1->copyFrom($frame->calledArgs[$op->arg2]);
                    break;
                case OpCode::TYPE_DECLARE_CLASS:
                    $name = $frame->scope[$op->arg1]->toString();
                    $lcname = strtolower($name);
                    if (isset($context->classes[$lcname])) {
                        throw new \LogicException("Duplicate class definition for $name");
                    }
                    $classEntry = new ClassEntry($name);
                    self::defineClass($context, $classEntry, $op->block1);
                    $context->classes[$lcname] = $classEntry;
                    break;
                case OpCode::TYPE_NEW:
                    $result = $frame->scope[$op->arg1];
                    $name = $frame->scope[$op->arg2]->toString();
                    $lcname = strtolower($name);
                    if (!isset($context->classes[$lcname])) {
                        throw new \LogicException("Attempting to instantiate non-existing class $name");
                    }
                    $class = $context->classes[$lcname];
                    $result->object(new ObjectEntry($class));
                    $frame->call = $result->toObject()->constructor;
                    $frame->callArgs = [$result];
                    break;
                case OpCode::TYPE_PROPERTY_FETCH:
                    $result = $frame->scope[$op->arg1];
                    $var = $frame->scope[$op->arg2]->resolveIndirect();
                    $name = $frame->scope[$op->arg3]->toString();
                    if ($var->type !== Variable::TYPE_OBJECT) {
                        throw new \LogicException("Unsupported property fetch on non-object");
                    }
                    $result->indirect($var->toObject()->getProperty($name));
                    break;
                default:
                    throw new \LogicException("VM OpCode Not Implemented: " . $op->getType());
            }
        }
        return self::SUCCESS;
    }

    protected static function defineClass(Context $context, ClassEntry $entry, Block $block): void {
        $frame = $block->getFrame($context);
        // TODO
        foreach ($block->opCodes as $op) {
            switch ($op->type) {
                case OpCode::TYPE_DECLARE_PROPERTY:
                    $name = $frame->scope[$op->arg1];
                    assert(is_null($op->arg2)); // no defaults for now
                    $entry->properties[] = new VM\ClassProperty(
                        $name->toString(),
                        null,
                        $frame->scope[$op->arg3]
                    );
                    break;
                default:
                    var_dump($op);
                    throw new \LogicException('Other class body types are not jittable for now');
            }
            
        }
    }

}