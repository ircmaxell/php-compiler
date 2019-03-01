<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\Backend\VM;

use PHPCfg\Parser;
use PHPCfg\Traverser;
use PHPCfg\LivenessDetector;
use PHPCfg\Visitor;
use PHPCfg\Printer as CfgPrinter;
use PHPTypes\TypeReconstructor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use PHPTypes\State;


class Runtime {
    private Compiler $compiler;
    private Parser $parser;
    private Traverser $preprocessor;
    private Traverser $postprocessor;
    private LivenessDetector $detector;
    private Optimizer $assignOpResolver;

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
    }

    public function parseAndCompile(string $code, string $filename): ?Block {
        $script = $this->parser->parse($code, $filename);
        $this->preprocessor->traverse($script);
        $this->typeReconstructor->resolve(new State($script));
        echo "\n\nPre Processed:\n\n";
        echo (new CfgPrinter\Text)->printScript($script);
        echo "\n\n";
        $this->postprocessor->traverse($script);
        $this->detector->detect($script);
        echo "\n\nPost Processed:\n\n";
        echo (new CfgPrinter\Text)->printScript($script);
        echo "\n\n";
        $block = $this->compiler->compile($script);
        echo "\n\nCompiled:\n\n";
        echo (new Printer)->print($block);
        echo "\n\n";
        $this->assignOpResolver->optimize($block);

        echo "\n\Optimized:\n\n";
        echo (new Printer)->print($block);
        echo "\n\n";
        return $block;
    }

    public function run(?Block $block) {
        if (!\is_null($block->handler)) {
            ($block->handler->callback)();
            return VM::SUCCESS;
        }
        return VM::run($block);
    }

    public function jit(?Block $block, ?string $debugFile = null) {
        JIT::compile($block, $debugFile);
    }

}