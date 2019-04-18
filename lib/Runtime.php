<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCfg\Func as CfgFunc;
use PHPCfg\Parser;
use PHPCfg\Traverser;
use PHPCfg\LivenessDetector;
use PHPCfg\Visitor;
use PHPCfg\Script;
use PHPTypes\TypeReconstructor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use PHPTypes\State;
use PHPCompiler\VM\Optimizer;
use PHPCompiler\VM\Context as VMContext;
use PHPCompiler\JIT\Context as JITContext;

class Runtime {
    const MODE_NORMAL   = 0b0001;
    const MODE_AOT      = 0b0010;

    public Compiler $compiler;
    public Parser $parser;
    public Traverser $preprocessor;
    public Traverser $postprocessor;
    public LivenessDetector $detector;
    public Optimizer $assignOpResolver;
    public VMContext $vmContext;
    public VM $vm;
    private ?JITContext $jitContext = null;
    private ?JIT $jit = null;
    public array $modules = [];
    public int $mode;
    public ?string $debugFile = null;

    public function __construct(int $mode = self::MODE_NORMAL) {
        $this->mode = $mode;
        $astTraverser = new NodeTraverser;
        $astTraverser->addVisitor(
            new NodeVisitor\NameResolver
        );
        $this->parser = new Parser(
            (new ParserFactory)->create(ParserFactory::ONLY_PHP7), 
            $astTraverser
        );

        $this->preprocessor = new Traverser;
        $this->preprocessor->addVisitor(new Visitor\Simplifier);
        $this->preprocessor->addVisitor(new Visitor\CallFinder);
        $this->preprocessor->addVisitor(new Visitor\DeadBlockEliminator);
        $this->preprocessor->addVisitor(new Visitor\DeclarationFinder);
        $this->preprocessor->addVisitor(new Visitor\VariableFinder);
        $this->postprocessor = new Traverser;
        $this->postprocessor->addVisitor(new Visitor\PhiResolver);
        $this->detector = new LivenessDetector;
        $this->assignOpResolver = new Optimizer\AssignOp;

        $this->typeReconstructor = new TypeReconstructor;
        $this->compiler = new Compiler;

        $this->vmContext = new VMContext($this);
        $this->vm = new VM($this->vmContext);
        $this->loadCoreModules();
    }

    public function __destruct() {
        foreach ($this->modules as $module) {
            $module->shutdown();
        }
    }

    public function setDebug(?string $debugFile = null): void {
        $this->debugFile = $debugFile;
    }

    private function loadCoreModules(): void {
        $this->load(new ext\types\Module);
        $this->load(new ext\standard\Module);
    }

    public function loadJit(): JIT {
        if (is_null($this->jit)) {
            $this->jit = new JIT(
                $this->loadJitContext()
            );
            foreach ($this->modules as $module) {
                foreach ($module->getFunctions() as $func) {
                    $this->jit->compileFunc($func);
                }
            }
        }
        return $this->jit;
    }

    public function loadJitContext(): JITContext {
        if (is_null($this->jitContext)) {
            $this->jitContext = new JITContext(
                $this,
                $this->mode === self::MODE_NORMAL ? JIT\Builtin::LOAD_TYPE_EMBED : JIT\Builtin::LOAD_TYPE_STANDALONE
            );
            if (!is_null($this->debugFile)) {
                $this->jitContext->setDebugFile($this->debugFile);
            }
        }
        return $this->jitContext;
    }

    public function load(Module $module): void {
        $this->modules[] = $module;
        $module->init($this);
        foreach ($module->getFunctions() as $function) {
            $this->vmContext->declareFunction($function);
        }
    }

    public function parse(string $code, string $filename): Script {
        $script = $this->parser->parse($code, $filename);
        $this->preprocessor->traverse($script);
        $state = new State($script);
        $this->typeReconstructor->resolve($state);
        $this->postprocessor->traverse($script);
        $this->detector->detect($script);
        return $script;
    }

    public function compile(Script $script): ?Block {
        $block = $this->compiler->compile($script);
        $this->assignOpResolver->optimize($block);
        return $block;
    }

    public function compileFunc(string $name, CfgFunc $func): Func {
        $compiled = $this->compiler->compileFunc($name, $func);
        $this->assignOpResolver->optimize($compiled->block);
        return $compiled;
    }

    public function jit(?Block $block) {
        $this->loadJit()->compile($block);
        $this->loadJitContext()->compileInPlace();
    }

    public function standalone(?Block $block, string $outfile) {
        $context = $this->loadJitContext();
        $context->setMain($this->loadJit()->compile($block));
        $context->compileToFile($outfile);
    }

    public function parseAndCompile(string $code, string $filename): ?Block {
        return $this->compile($this->parse($code, $filename));
    }

    public function parseAndCompileFile(string $filename): ?Block {
        return $this->compile($this->parse(file_get_contents($filename), $filename));
    }

    public function run(?Block $block) {
        return $this->vm->run($block);
    }

}
