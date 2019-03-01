<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

use PHPTypes\Type;

class VM {
    const SUCCESS = 1;
    const FAILURE = 2;

    public static function run(Block $block, ?Context $context = null): int {
        $context = $context ?? new Context;
        $context->push($block->getFrame($context));
nextframe:
        $frame = $context->pop();

        if (is_null($frame)) {
            return self::SUCCESS;
        }
restart:
        if (!is_null($block->handler)) {
            ($block->handler->callback)();
            goto nextframe;
        }

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
                case OpCode::TYPE_SMALLER:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2]->toInt();
                    $arg3 = $frame->scope[$op->arg3]->toInt();
                    $arg1->type = Type::TYPE_BOOLEAN;
                    $arg1->bool = $arg2 < $arg3;
                    break;
                case OpCode::TYPE_PLUS:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2]->toInt();
                    $arg3 = $frame->scope[$op->arg3]->toInt();
                    $arg1->type = Type::TYPE_LONG;
                    $arg1->integer = $arg2 + $arg3;
                    break;
                case OpCode::TYPE_CONCAT:
                    $arg1 = $frame->scope[$op->arg1];
                    $arg2 = $frame->scope[$op->arg2]->toString();
                    $arg3 = $frame->scope[$op->arg3]->toString();
                    $arg1->type = Type::TYPE_STRING;
                    $arg1->string = $arg2 . $arg3;
                    break;
                case OpCode::TYPE_ECHO:
                    echo $frame->scope[$op->arg1]->toString();
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
                case OpCode::TYPE_FUNCDEF:
                    $name = $frame->scope[$op->arg1]->toString();
                    $lcname = strtolower($name);
                    if (isset($context->functions[$lcname])) {
                        throw new \LogicException("Duplicate function definition for $lcname()");
                    }
                    $context->functions[$lcname] = $op->block1;
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
                case OpCode::TYPE_FUNCCALL_EXEC_NORETURN:
                    $new = $frame->call->getFrame($context);
                    $new->calledArgs = $frame->callArgs;
                    $context->push($frame); // save the frame
                    $frame = $new;
                    goto restart;
                case OpCode::TYPE_ARG_RECV:
                    // Todo: do type checks and transformations
                    $arg1 = $frame->scope[$op->arg1];
                    $arg1->copyFrom($frame->calledArgs[$op->arg2]);
                    break;
                default:
                    throw new \LogicException("VM OpCode Not Implemented: " . $op->getType());
            }
        }
        return self::SUCCESS;
    }

}