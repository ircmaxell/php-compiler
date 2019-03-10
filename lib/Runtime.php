<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCfg\Parser;
use PHPCfg\Traverser;
use PHPCfg\LivenessDetector;
use PHPCfg\Visitor;
use PHPCfg\Printer as CfgPrinter;
use PHPCfg\Script;
use PHPTypes\TypeReconstructor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use PHPTypes\State;
use PHPCompiler\VM\Optimizer;
use PHPCompiler\VM\Context as VMContext;

class Runtime {
    private Compiler $compiler;
    private Parser $parser;
    private Traverser $preprocessor;
    private Traverser $postprocessor;
    private LivenessDetector $detector;
    private Optimizer $assignOpResolver;
    private VMContext $vmContext;
    private array $modules = [];

    public function __construct() {
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

        $this->vmContext = new VMContext;
        $this->loadCoreModules();
    }

    public function __destruct() {
        foreach ($this->modules as $module) {
            $module->shutdown();
        }
    }

    private function loadCoreModules(): void {
        $this->load(new ext\standard\Module);
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
        $this->typeReconstructor->resolve(new State($script));
        $this->postprocessor->traverse($script);
        $this->detector->detect($script);
        return $script;
    }

    public function compile(Script $script): ?Block {
        $block = $this->compiler->compile($script);
        $this->assignOpResolver->optimize($block);
        return $block;
    }

    public function jit(?Block $block, ?string $debugFile = null) {
        JIT::compile($block, JIT\Builtin::LOAD_TYPE_EMBED, $debugFile)->compileInPlace();
    }

    public function standalone(?Block $block, string $outfile, ?string $debugFile = null) {
        JIT::compile($block, JIT\Builtin::LOAD_TYPE_STANDALONE, $debugFile)->compileToFile($outfile);
    }

    public function parseAndCompile(string $code, string $filename): ?Block {
        return $this->compile($this->parse($code, $filename));
    }

    public function run(?Block $block) {
        return VM::run($block, $this->vmContext);
    }

}