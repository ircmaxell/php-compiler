<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\JIT;

use PHPCfg\Operand;
use PHPCompiler\Runtime;
use PHPCompiler\Handler;
use PHPCompiler\Block;
use PHPCompiler\Handler\Builtins;
use PHPTypes\Type;

use PHPLLVM;

class Context {

    public PHPLLVM\LLVM $llvm;
    public PHPLLVM\Context $context;
    public PHPLLVM\Module $module;
    public PHPLLVM\BasicBlock $initBlock;
    public PHPLLVM\BasicBlock $shutdownBlock;
    public PHPLLVM\Builder $builder;

    public ?PHPLLVM\Value\Function_ $main = null;
    public ?PHPLLVM\Value\Function_ $initFunc = null;
    public ?PHPLLVM\Value\Function_ $shutdownFunc = null;

    public array $functionScope = [];
    private array $typeMap = [];
    public array $structFieldMap = [];
    private array $intConstant = [];
    private array $stringConstant = [];
    private array $builtins;
    private array $stringConstantMap = [];
    
    private ?Result $result = null;
    public Builtin\MemoryManager $memory;
    public Builtin\Output $output;
    public Builtin\Type $type;
    public Builtin\Refcount $refcount;
    public Builtin\ErrorHandler $error;
    public int $loadType;
    private static int $stringConstantCounter = 0;
    private ?string $debugFile = null;

    public Scope $scope;
    private array $exports = [];
    public Runtime $runtime;

    public int $mode;
    public Analyzer $analyzer;

    public array $attributes;

    public function __construct(Runtime $runtime, int $loadType) {
        $this->runtime = $runtime;
        $this->scope = new Scope;
        $this->loadType = $loadType;
        $this->llvm = PHPLLVM\Chooser::choose();
        $this->llvm->initializeNative();
        $this->context = $this->llvm->contextCreate();
        $this->module = $this->context->moduleCreateWithName('main');
        $this->builder = $this->context->builderCreate();

        $this->attributes = [
            'alwaysinline' => $this->context->createEnumAttribute($this->context->getEnumAttributeKindForName('alwaysinline'), 0),
            'nocapture' => $this->context->createEnumAttribute($this->context->getEnumAttributeKindForName('nocapture'), 0),
            'readnone' => $this->context->createEnumAttribute($this->context->getEnumAttributeKindForName('readnone'), 0),
            'readonly' => $this->context->createEnumAttribute($this->context->getEnumAttributeKindForName('readonly'), 0),
            'writeonly' => $this->context->createEnumAttribute($this->context->getEnumAttributeKindForName('writeonly'), 0),
        ];

        $this->analyzer = new Analyzer;
        
        $this->refcount = new Builtin\Refcount($this, $loadType);
        $this->output = new Builtin\Output($this, $loadType);
        $this->type = new Builtin\Type($this, $loadType);
        $this->internal = new Builtin\Internal($this, $loadType);
        $this->vararg = new Builtin\VarArg($this, $loadType);
        $this->error = new Builtin\ErrorHandler($this, $loadType);

        $this->defineBuiltins($loadType);
    }

    public function setMain(PHPLLVM\Value\Function_ $func): void {
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

    public function registerBuiltin(Builtin $builtin): void {
        $this->builtins[] = $builtin;
    }

    private function defineBuiltins(int $loadType): void {
        foreach ($this->builtins as $builtin) {
            // this is a separate loop, since implementation may
            // depend on global variables set during init()
            // so this way, cross-builtin dependencies are honored
            $builtin->register();
        }
        if ($loadType === Builtin::LOAD_TYPE_IMPORT) {
            return;
        }
        foreach ($this->builtins as $builtin) {
            // this is a separate loop, since initialize may
            // depend on functions defined during implement()
            // so this way, cross-builtin dependencies are honored
            $builtin->implement();
        }
        $signature = $this->context->functionType(
            $this->context->voidType(),
            false
        );
        $this->initFunc = $this->module->addFunction('__init__', $signature);
        $this->initBlock = $this->initFunc->appendBasicBlock('main');

        $this->shutdownFunc = $this->module->addFunction('__shutdown__', $signature);
        $this->shutdownBlock = $this->shutdownFunc->appendBasicBlock('main');

        foreach ($this->builtins as $builtin) {
            $builtin->initialize();
        }
    }

    public function compileToFile(string $file) {
        // add main function
        if (!is_null($this->main)) {
            $signature = $this->context->functionType($this->context->voidType(), false);
            $main = $this->module->addFunction('main', $signature);
            $block = $main->appendBasicBlock('main');
            $this->builder->positionAtEnd($block);
            $this->builder->call($this->initFunc);
            $this->builder->call($this->main);
            $this->builder->call($this->shutdownFunc);
            $this->builder->returnVoid();
        }
        $this->compileCommon();
        $engine = $this->module->createExecutionEngine();
        $machine = $engine->getTargetMachine();
        $machine->emitToFile($this->module, $file, $machine::CODEGEN_FILE_TYPE_OBJECT);
    }

    public function compileInPlace() {
        if (is_null($this->result)) {
            $this->compileCommon();

            $this->result = new Result(
                $this->module->createJITCompiler(3),
                $this->loadType
            );
            foreach ($this->exports as $export) {
                $export[2]->handler = $this->result->getHandler($export[0], $export[1]);
            }
        }
    }

    private function compileCommon() {
        foreach ($this->builtins as $builtin) {
            $builtin->shutdown();
        }
        $this->builder->positionAtEnd($this->initBlock);
        $this->builder->returnVoid();
        $this->builder->positionAtEnd($this->shutdownBlock);
        $this->builder->returnVoid();

        if (!is_null($this->debugFile)) {
            $this->module->printToFile($this->debugFile . '.bc');
        }

        $this->module->verify($this->module::VERIFY_ACTION_THROW, $message);
        
    }

    public function setDebugFile(string $file): void {
        $this->debugFile = $file;
        $this->setDebug(true);
    }

    public function setDebug(bool $value): void {
        // Todo
    }

    public function lookupFunction(string $name): PHPLLVM\Value\Function_ {
        if (isset($this->functionScope[$name])) {
            return $this->functionScope[$name];
        }
        throw new \LogicException('Unable to lookup non-existing function ' . $name);
    }

    public function registerFunction(string $name, PHPLLVM\Value\Function_ $func): void {
        $this->functionScope[$name] = $func;
    }

    public function registerType(string $name, PHPLLVM\Type $type): void {
        $this->typeMap[$name] = $type;
    }

    public function castToBool(PHPLLVM\Value $value): PHPLLVM\Value {
        $type = $value->typeOf();
        switch ($this->getStringFromType($type)) {
            case 'bool':
            case 'int1':
                return $value;
            case 'unsigned int':
            case 'long long':
            case 'int32':
            case 'int64':
                return $this->builder->icmp($this->builder::INT_NE, $value, $type->constInt(0, false));
        }
        throw new \LogicException("Unknown bool cast from type: " . $this->getStringFromType($type));
    }

    public function getTypeFromType(Type $type): PHPLLVM\Type {
        static $map = [
            Type::TYPE_LONG => 'long long',
            Type::TYPE_STRING => '__string__*',
            Type::TYPE_OBJECT => '__object__*',
        ];
        if (isset($map[$type->type])) {
            return $this->getTypeFromString($map[$type->type]);
        }
        throw new \LogicException("Unsupported Type::TYPE: " . $type->toString());
    }

    public function getStringFromType(PHPLLVM\Type $type): string {
        foreach ($this->typeMap as $name => $ptr) {
            if ($type->toString() === $ptr->toString()) {
                return $name;
            }
        }
        // else, try to figure it out:
        switch ($type->getKind()) {
            case PHPLLVM\Type::KIND_DOUBLE:
                return 'double';
            case PHPLLVM\Type::KIND_INTEGER:
                return 'int' . $this->llvm->lib->LLVMGetIntTypeWidth($type->type);
        }
        return 'unknown';
    }

    public function getTypeFromString(string $type): PHPLLVM\Type {
        if (!isset($this->typeMap[$type])) {
            $this->typeMap[$type] = $this->_getTypeFromString($type);
        }
        return $this->typeMap[$type];
    }

    public function _getTypeFromString(string $type): PHPLLVM\Type {
        switch ($type) {
            case 'void':
                return $this->context->voidType();
            case 'const char':
                return $this->context->int8Type();
            case 'char':
                return $this->context->int8Type();
            case 'int32':
            case 'int':
            case 'unsigned int':
                return $this->context->int32Type();
            case 'int64':
            case 'long long':
            case 'unsigned long long':
                return $this->context->int64Type();
            case 'size_t':
                return $this->module->getModuleDataLayout()->intPointerType();
            case 'int1':
            case 'bool':
                return $this->context->int1Type();
            case 'double':
                return $this->context->doubleType();

        }
        if (substr($type, -1) === '*') {
            return $this->getTypeFromString(substr($type, 0, -1))->pointerType(0);
        }
        if (substr($type, -1) === ']') {
            // array type
            if (preg_match('(^(.*?)\\[(\d+)\\]$)', $type, $match)) {
                return $this->getTypeFromString($match[1])->arrayType((int) $match[2]);
            } else {
                throw new \LogicException("Could not parse type with array notation: $type");
            }
        }
        throw new \LogicException("Unsupported native type $type");
    }

    public function constantFromInteger(int $value, ?string $type = null): PHPLLVM\Value {
        return $this->getTypeFromString($type === null ? 'long long' : $type)->constInt($value, false);
    }

    public function constantFromFloat(float $value, ?string $type = null): PHPLLVM\Value {
        return $this->getTypeFromString($type === null ? 'double' : $type)->constReal($value);
    }

    public function constantFromString(string $string): PHPLLVM\Value {
        if (!isset($this->stringConstant[$string])) {
            $const = $this->context->constString($string, true);
            $global = $this->module->addGlobal($const->typeOf(), $string);
            $global->setInitializer($const);
            $this->stringConstant[$string] = $global;
        }
        return $this->stringConstant[$string];
    }

    private array $boolValues = [];

    public function constantFromBool(bool $value): PHPLLVM\Value {
        $id = $value ? 1 : 0;
        if (!isset($this->boolValues[$id])) {
            $this->boolValues[$id] = $this->getTypeFromString('bool')->constInt($id, false);
        }
        return $this->boolValues[$id];
    }

    public function constantStringFromString(string $string): PHPLLVM\Value {
        if (!isset($this->stringConstantMap[$string])) {
            $global = $this->module->addGlobal($this->type->string->pointer, 'string_const_' . count($this->stringConstantMap));
            $oldBuilder = $this->builder;
            $this->builder = $this->context->builderCreate();
            $this->builder->positionAtEnd($this->initBlock);
            $this->type->string->init(
                $global,
                $this->constantFromString($string),
                $this->constantFromInteger(strlen($string), 'size_t'),
                true
            );
            $this->builder->positionAtEnd($this->shutdownBlock);
            $this->builder->free($global);
            $this->builder = $oldBuilder;
            $this->stringConstantMap[$string] = $global;
        }
        return $this->stringConstantMap[$string];
    }

    public function makeVariableFromOp(
        PHPLLVM\Value\Function_ $func,
        PHPLLVM\BasicBlock $basicBlock,
        Block $block,
        Operand $op
    ) {
        assert(!$this->scope->variables->contains($op));
        $this->scope->variables[$op] = Variable::fromOp($this, $func, $basicBlock, $block, $op);
        $this->scope->variables[$op]->initialize($basicBlock);
    }

    public function setVariableOp(Operand $op, Variable $var) {
        assert(!$this->scope->variables->contains($op));
        $this->scope->variables[$op] = $var;
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

    public function makeVariableFromValueOp(
        PHPLLVM\Value $value,
        Operand $op
    ): Variable {
        $this->scope->variables[$op] = Variable::fromRValueOp(
            $this, $value, $op
        );
        return $this->scope->variables[$op];
    }

    public function freeDeadVariables(
        PHPLLVM\Value\Function_ $func,
        PHPLLVM\BasicBlock $basicBlock,
        Block $block
    ): void {
        foreach ($block->orig->deadOperands as $op) {
            $this->scope->variables[$op]->free($basicBlock);
        }
    }

}