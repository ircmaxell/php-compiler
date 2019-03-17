<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT\Builtin;

use PHPCompiler\JIT\Builtin;
use PHPCompiler\Block;
use PHPCfg\Op;

class ErrorHandler extends Builtin {
    const E_NORMAL = 0;
    const E_ERROR = 1;
    const E_RECOVERABLE_ERROR = 2;

    private \gcc_jit_struct_ptr $struct;
    private \gcc_jit_type_ptr $type;
    private array $fields;
    private \gcc_jit_lvalue_ptr $global;

    private int $leaveBlockId = 1;

    public function register(): void {
        $this->fields = [
            'type' => $this->context->helper->createField('type', 'int'),
            'message' => $this->context->helper->createField('message', '__string__*')
        ];
        $this->struct = \gcc_jit_context_new_struct_type(
            $this->context->context,
            null,
            '__error__',
            count($this->fields),
            \gcc_jit_field_ptr_ptr::fromArray(...array_values($this->fields))
        );
        $this->type = \gcc_jit_struct_as_type($this->struct);
        $this->context->registerType(
            '__error__',
            $this->type
        );
        $this->global = \gcc_jit_context_new_global(
            $this->context->context,
            $this->context->location(),
            \GCC_JIT_GLOBAL_EXPORTED,
            $this->type,
            'current_error_status'
        );
    }

    public function initialize(): void {
        \gcc_jit_block_add_assignment(
            $this->context->initBlock,
            $this->context->location(),
            gcc_jit_lvalue_access_field(
                $this->global,
                $this->context->location(),
                $this->fields['type']
            ),
            $this->context->constantFromInteger(self::E_NORMAL, 'int')
        );
    }

    public function returnOnError(\gcc_jit_function_ptr $func, \gcc_jit_block_ptr $gccBlock, Block $block): \gcc_jit_block_ptr {
        $leave = \gcc_jit_function_new_block($func, 'error_' . ($this->leaveBlockId));
        $stay = \gcc_jit_function_new_block($func, 'noterror_' . (++$this->leaveBlockId));
        \gcc_jit_block_end_with_conditional(
            $gccBlock, 
            $this->context->location(), 
            \gcc_jit_context_new_comparison(
                $this->context->context,
                $this->context->location(),
                \GCC_JIT_COMPARISON_NE,
                gcc_jit_rvalue_access_field(
                    $this->global->asRValue(),
                    $this->context->location(),
                    $this->fields['type']
                ),
                $this->context->constantFromInteger(self::E_NORMAL, 'int')
            ),
            $leave,
            $stay
        );
        $this->context->freeDeadVariables($func, $leave, $block);
        if (!is_null($block->func)) {
            if ($block->func->returnType instanceof Op\Type\Literal) {
                var_dump($block->func->returnType->name);
                switch ($block->func->returnType->name) {
                    case 'void':
                        \gcc_jit_block_end_with_void_return($leave, $this->context->location());
                        break;
                    case 'int':
                        \gcc_jit_block_end_with_return($leave, $this->context->location(), $this->context->constantFromInteger(0));
                        break;
                    case 'string':
                        \gcc_jit_block_end_with_return($leave, $this->context->location(), $this->context->type->string->nullPointer());
                        break;
                    default:
                        throw new \LogicException("Non-void return types not supported yet");
                }
            } else {
                throw new \LogicException("Non-typed functions not implemented yet");
            }
        } else {
            \gcc_jit_block_end_with_void_return($leave, $this->context->location());
        }
        return $stay;
    }

    public function error(\gcc_jit_block_ptr $block, int $level, string $message): void {
        \gcc_jit_block_add_assignment(
            $block,
            $this->context->location(),
            gcc_jit_lvalue_access_field(
                $this->global,
                $this->context->location(),
                $this->fields['type']
            ),
            $this->context->constantFromInteger($level, 'int')
        );
        \gcc_jit_block_add_assignment(
            $block,
            $this->context->location(),
            gcc_jit_lvalue_access_field(
                $this->global,
                $this->context->location(),
                $this->fields['message']
            ),
            $this->context->constantStringFromString($message)
        );
    }
}