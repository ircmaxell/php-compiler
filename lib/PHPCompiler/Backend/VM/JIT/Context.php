<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM\JIT;

use PHPCompiler\Backend\VM\Handler;

use PHPTypes\Type;

class Context {

    public \gcc_jit_context_ptr $context;
    public array $functionScope = [];
    private array $typeMap = [];
    private array $intConstant = [];
    private array $stringConstant = [];
    private ?Result $result = null;
    public Builtin\MemoryManager $memory;
    public Builtin\Output $output;
    public Builtin\Type $type;
    public Builtin\Refcount $refcount;
    public Helper $helper;
    private array $builtins;
    private int $loadType;

    public function __construct(int $loadType) {
        $this->loadType = $loadType;
        $this->context = \gcc_jit_context_acquire();
        $this->helper = new Helper($this);
        $this->refcount = new Builtin\Refcount($this, $loadType);
        $this->memory = new Builtin\MemoryManager($this, $loadType);
        $this->output = new Builtin\Output($this, $loadType);
        $this->type = new Builtin\Type($this, $loadType);

        if ($loadType !== Builtin::LOAD_TYPE_IMPORT) {
            $this->defineBuiltins();
        }   
    }

    public function registerBuiltin(Builtin $builtin): void {
        $this->builtins[] = $builtin;
    }

    public function __destruct() {
        \gcc_jit_context_release($this->context);
    }

    private function defineBuiltins(): void {
      foreach ($this->builtins as $builtin) {
          // this is a separate loop, since implementation may
          // depend on global variables set during init()
          // so this way, cross-builtin dependencies are honored
          $builtin->implement();
      }
      $initFunc = \gcc_jit_context_new_function(
          $this->context,
          null,
          \GCC_JIT_FUNCTION_EXPORTED,
          $this->getTypeFromString('void'),
          '__init__',
          0,
          null,
          0
      );

      $block = \gcc_jit_function_new_block($initFunc, 'initblock');
      foreach ($this->builtins as $builtin) {
          $block = $builtin->init($initFunc, $block);
      }
      \gcc_jit_block_end_with_void_return($block, null);
      
    }

    public function compileInPlace(): Result {
        if (is_null($this->result)) {
            $this->result = new Result(
                \gcc_jit_context_compile($this->context),
                $this->loadType
            );
        }
        return $this->result;
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

    public function getTypeFromType(Type $type): \gcc_jit_type_ptr {
        static $map = [
            Type::TYPE_LONG => 'long long',
            Type::TYPE_STRING => 'char*',
        ];
        if (isset($map[$type->type])) {
            return $this->getTypeFromString($map[$type->type]);
        }
        throw new \LogicException("Unsupported Type::TYPE: " . $type->toString());
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
            'size_t' => \GCC_JIT_TYPE_SIZE_T,
            'uint32_t' => \GCC_JIT_TYPE_UNSIGNED_LONG,
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

    public function constantFromInteger(int $value): \gcc_jit_rvalue_ptr {
        if (!isset($this->intConstant[$value])) {
            $this->intConstant[$value] = \gcc_jit_context_new_rvalue_from_long(
                $this->context,
                $this->getTypeFromString('long long'),
                $value
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

}