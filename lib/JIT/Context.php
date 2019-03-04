<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

use PHPCfg\Operand;
use PHPCompiler\Handler;
use PHPCompiler\Block;
use PHPTypes\Type;

class Context {

    public \gcc_jit_context_ptr $context;
    public \gcc_jit_block_ptr $initBlock;
    public \gcc_jit_block_ptr $shutdownBlock;
    public array $functionScope = [];
    private array $typeMap = [];
    private array $intConstant = [];
    private array $stringConstant = [];
    private array $builtins;
    private array $stringConstantMap = [];
    
    private ?Result $result = null;
    public Builtin\MemoryManager $memory;
    public Builtin\Output $output;
    public Builtin\Type $type;
    public Builtin\Refcount $refcount;
    public Helper $helper;
    private int $loadType;
    private static int $stringConstantCounter = 0;
    private ?string $debugFile = null;

    public Scope $scope;
    private ?\gcc_jit_function_ptr $main = null;
    private ?\gcc_jit_function_ptr $initFunc = null;
    private ?\gcc_jit_function_ptr $shutdownFunc = null;

    private array $exports = [];

    public function __construct(int $loadType) {
        $this->scope = new Scope;
        $this->loadType = $loadType;
        $this->context = \gcc_jit_context_acquire();
        $this->helper = new Helper($this);
        $this->location = new Location("Unknown", 1, 1);
        
        $this->refcount = new Builtin\Refcount($this, $loadType);
        $this->memory = Builtin\MemoryManager::load($this, $loadType);
        $this->output = new Builtin\Output($this, $loadType);
        $this->type = new Builtin\Type($this, $loadType);

        $this->defineBuiltins($loadType);
    }

    public function __destruct() {
        \gcc_jit_context_release($this->context);
    }

    public function setMain(\gcc_jit_function_ptr $func): void {
        $this->main = $func;
    }

    public function addExport(string $name, string $signature, Block $block): void {
        $this->exports[] = [$name, $signature, $block];
    }

    public function pushScope(): void {
        $this->scopeStack[] = $this->scope;
        $this->scope = new Scope;
    }

    public function popScope(): void {
        assert(!empty($this->scopeStack));
        $this->scope = array_pop($this->scopeStack);
    }

    public function location(): \gcc_jit_location_ptr {
        if (is_null($this->location)) {
            return gcc_jit_context_new_location (
                $this->context,
                'Unknown',
                1,
                1
            );
        }
        return gcc_jit_context_new_location (
            $this->context,
            $this->location->filename,
            $this->location->line,
            $this->location->column
        );
    }

    public function registerBuiltin(Builtin $builtin): void {
        $this->builtins[] = $builtin;
    }

    private function defineBuiltins(int $loadType): void {
        foreach ($this->builtins as $builtin) {
            $this->location = new Location(get_class($builtin) . '::register', 1, 1, $this->location);
            // this is a separate loop, since implementation may
            // depend on global variables set during init()
            // so this way, cross-builtin dependencies are honored
            $builtin->register();
            $this->location = $this->location->prev;
        }
        if ($loadType === Builtin::LOAD_TYPE_IMPORT) {
            return;
        }
        foreach ($this->builtins as $builtin) {
            $this->location = new Location(get_class($builtin) . '::implement', 1, 1, $this->location);
            // this is a separate loop, since initialize may
            // depend on functions defined during implement()
            // so this way, cross-builtin dependencies are honored
            $builtin->implement();
            $this->location = $this->location->prev;
        }
        $this->initFunc = \gcc_jit_context_new_function(
            $this->context,
            null,
            \GCC_JIT_FUNCTION_EXPORTED,
            $this->getTypeFromString('void'),
            '__init__',
            0,
            null,
            0
        );

        $this->initBlock = \gcc_jit_function_new_block($this->initFunc, 'initblock');
        foreach ($this->builtins as $builtin) {
            $this->location = new Location(get_class($builtin) . '::initialize', 1, 1, $this->location);
            $builtin->initialize();
            $this->location = $this->location->prev;
        }
        $this->shutdownFunc = \gcc_jit_context_new_function(
            $this->context,
            null,
            \GCC_JIT_FUNCTION_EXPORTED,
            $this->getTypeFromString('void'),
            '__shutdown__',
            0,
            null,
            0
        );
        $this->shutdownBlock = \gcc_jit_function_new_block($this->shutdownFunc, 'shutdownblock');
      
    }

    public function compileToFile(string $file) {
        // add main function
        if (!is_null($this->main)) {
            $main = \gcc_jit_context_new_function(
                $this->context,
                null,
                \GCC_JIT_FUNCTION_EXPORTED,
                $this->getTypeFromString('void'),
                'main',
                0,
                null,
                0
            );
            $block = \gcc_jit_function_new_block($main, 'main');
            $this->helper->eval(
                $block,
                \gcc_jit_context_new_call(
                    $this->context,
                    $this->location(),
                    $this->initFunc,
                    0,
                    null
                )
            );
            $this->helper->eval(
                $block,
                \gcc_jit_context_new_call(
                    $this->context,
                    $this->location(),
                    $this->main,
                    0,
                    null
                )
            );
            $this->helper->eval(
                $block,
                \gcc_jit_context_new_call(
                    $this->context,
                    $this->location(),
                    $this->shutdownFunc,
                    0,
                    null
                )
            );
            \gcc_jit_block_end_with_void_return($block, null);
        }
        $this->compileCommon();
        \gcc_jit_context_compile_to_file(
            $this->context,
            \GCC_JIT_OUTPUT_KIND_EXECUTABLE,
            $file
        );
    }

    public function compileInPlace() {
        if (is_null($this->result)) {
            $this->compileCommon();

            $this->result = new Result(
                \gcc_jit_context_compile($this->context),
                $this->loadType
            );
            foreach ($this->exports as $export) {
                $export[2]->handler = $this->result->getHandler($export[0], $export[1]);
            }
        }
    }

    private function compileCommon() {
        foreach ($this->builtins as $builtin) {
            $this->location = new Location(get_class($builtin) . '::shutdown', 1, 1, $this->location);
            $builtin->shutdown();
            $this->location = $this->location->prev;
        }
        \gcc_jit_block_end_with_void_return($this->initBlock, $this->location());
        \gcc_jit_block_end_with_void_return($this->shutdownBlock, $this->location());
        if (!is_null($this->debugFile)) {
            gcc_jit_context_dump_reproducer_to_file(
                $this->context,
                $this->debugFile . '.reproduce.c'
            );
            \gcc_jit_context_dump_to_file(
                $this->context,
                $this->debugFile . '.debug.c',
                1
            );
        }
    }

    public function setDebugFile(string $file): void {
        $this->debugFile = $file;
    }

    public function setDebug(bool $value): void {
        \gcc_jit_context_set_bool_option(
            $this->context,
            \GCC_JIT_BOOL_OPTION_DEBUGINFO,
            $value ? 1 : 0
        );
    }

    public function setOption(int $option, $value) {
        if (is_int($value)) {
            \gcc_jit_context_set_int_option(
                $this->context,
                $option,
                $value
            );
        } else {
            throw new \LogicException("Unsupported option type " . gettype($value));
        }
    }

    public function lookupFunction(string $name): Func {
        if (isset($this->functionScope[$name])) {
            return $this->functionScope[$name];
        }
        throw new \LogicException('Unable to lookup non-existing function ' . $name);
    }

    public function registerFunction(string $name, Func $func): void {
        $this->functionScope[$name] = $func;
    }

    public function registerType(string $name, \gcc_jit_type_ptr $type): void {
        $this->typeMap[$name] = $type;
    }

    public function castToBool(\gcc_jit_rvalue_ptr $value): \gcc_jit_rvalue_ptr {
        $type = \gcc_jit_rvalue_get_type($value);
        switch ($this->getStringFromType($type)) {
            case 'bool':
                return $value;
            case 'long long':
                return \gcc_jit_context_new_comparison(
                    $this->context,
                    $this->location(),
                    \GCC_JIT_COMPARISON_NE,
                    $value,
                    $this->constantFromInteger(0, 'long long')
                );
        }
    }

    public function getTypeFromType(Type $type): \gcc_jit_type_ptr {

        static $map = [
            Type::TYPE_LONG => 'long long',
            Type::TYPE_STRING => '__string__*',
        ];
        if (isset($map[$type->type])) {
            return $this->getTypeFromString($map[$type->type]);
        }
        throw new \LogicException("Unsupported Type::TYPE: " . $type->toString());
    }

    public function getStringFromType(\gcc_jit_type_ptr $type): string {
        foreach ($this->typeMap as $name => $ptr) {
            if ($type->equals($ptr)) {
                return $name;
            }
        }
        return 'unknown';
    }

    public function getTypeFromString(string $type): \gcc_jit_type_ptr {
        if (!isset($this->typeMap[$type])) {
            $this->typeMap[$type] = $this->_getTypeFromString($type);
        }
        return $this->typeMap[$type];
    }

    public function _getTypeFromString(string $type): \gcc_jit_type_ptr {
        static $map = [
            'void' => \GCC_JIT_TYPE_VOID,
            'void*' => \GCC_JIT_TYPE_VOID_PTR,
            'const char*' => \GCC_JIT_TYPE_CONST_CHAR_PTR,
            'char' => \GCC_JIT_TYPE_CHAR,
            'int' => \GCC_JIT_TYPE_INT,
            'long long' => \GCC_JIT_TYPE_LONG_LONG,
            'unsigned long long' => \GCC_JIT_TYPE_UNSIGNED_LONG_LONG,
            'size_t' => \GCC_JIT_TYPE_SIZE_T,
            'uint32_t' => \GCC_JIT_TYPE_UNSIGNED_LONG,
            'bool' => \GCC_JIT_TYPE_BOOL,
        ];
        if (isset($map[$type])) {
            return \gcc_jit_context_get_type (
                $this->context, 
                $map[$type]
            );
        }
        switch ($type) {
            case 'char*':
                return \gcc_jit_type_get_pointer(
                    $this->getTypeFromString('char')
                );
            case 'char[1]':
                return \gcc_jit_context_new_array_type(
                    $this->context,
                    null,
                    $this->getTypeFromString('char'),
                    1
                );
            default:
                throw new \LogicException("Unsupported native type $type");
        }
    }

    public function constantFromInteger(int $value, ?string $type = null): \gcc_jit_rvalue_ptr {
        if (!isset($this->intConstant[$value])) {
            $this->intConstant[$value] = \gcc_jit_context_new_rvalue_from_long(
                $this->context,
                $this->getTypeFromString('long long'),
                $value
            );
        }
        if (!is_null($type)) {
            return $this->helper->cast(
                $this->intConstant[$value],
                $type
            );
        }
        return $this->intConstant[$value];
    }

    public function constantFromString(string $string): \gcc_jit_rvalue_ptr {
        if (!isset($this->stringConstant[$string])) {
            $this->stringConstant[$string] = \gcc_jit_context_new_string_literal(
                $this->context,
                $string
            );
        }
        return $this->stringConstant[$string];
    }

    public function constantStringFromString(string $string): \gcc_jit_rvalue_ptr {
        if (!isset($this->stringConstantMap[$string])) {
            $global = \gcc_jit_context_new_global(
                $this->context,
                $this->location(),
                \GCC_JIT_GLOBAL_INTERNAL,
                $this->getTypeFromString('__string__*'),
                '__string__constant_' . (self::$stringConstantCounter++)
            );
            $length = $this->constantFromInteger(strlen($string), 'size_t');
            $this->type->string->init(
                $this->initBlock,
                $global,
                $this->constantFromString($string),
                $length,
                true
            );
            $this->stringConstantMap[$string] = $global;
            $this->memory->free(
                $this->shutdownBlock,
                $global->asRValue()
            );
        }
        return $this->stringConstantMap[$string]->asRValue();
    }

    public function makeVariableFromOp(
        \gcc_jit_function_ptr $func,
        \gcc_jit_block_ptr $gccBlock,
        Block $block,
        Operand $op
    ) {
        assert(!$this->scope->variables->contains($op));
        $this->scope->variables[$op] = Variable::fromOp($this, $func, $gccBlock, $block, $op);
        $this->scope->variables[$op]->initialize($gccBlock);
    }

    public function hasVariableOp(Operand $op): bool {
        if ($this->scope->variables->contains($op)) {
            return true;
        }
        if ($op instanceof Operand\Literal) {
            return true;
        }
        return false;
    }

    public function getVariableFromOp(Operand $op): Variable {
        if (!$this->scope->variables->contains($op)) {
            if ($op instanceof Operand\Literal) {
                $this->scope->variables[$op] = Variable::fromLiteral($this, $op);
            } else {
                throw new \LogicException("Unknown variable referenced: " . get_class($op));
            }
        }
        return $this->scope->variables[$op];
    }

    public function makeVariableFromRValueOp(
        \gcc_jit_rvalue_ptr $rvalue,
        Operand $op
    ): Variable {
        $this->scope->variables[$op] = Variable::fromRValueOp(
            $this, $rvalue, $op
        );
        return $this->scope->variables[$op];
    }

    public function freeDeadVariables(
        \gcc_jit_function_ptr $func,
        \gcc_jit_block_ptr $gccBlock,
        Block $block
    ): void {
        foreach ($block->orig->deadOperands as $op) {
            $this->scope->variables[$op]->free($gccBlock);
        }
    }

}