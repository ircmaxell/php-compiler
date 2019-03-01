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
use PHPCfg\Printer;
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

        $this->typeReconstructor = new TypeReconstructor;
        $this->compiler = new Compiler;
    }

    public function parseAndCompile(string $code, string $filename): ?Block {
        $script = $this->parser->parse($code, $filename);
        $this->preprocessor->traverse($script);
        $this->typeReconstructor->resolve(new State($script));
        $this->postprocessor->traverse($script);
        $this->detector->detect($script);
        echo (new Printer\Text)->printScript($script);
        echo "\n\n";
        return $this->compiler->compile($script);
    }

    public function run(?Block $opcodes) {
        return VM::run($opcodes, new Context);
    }

    public function jit(?Block $opcodes, ?string $debugFile = null) {
        JIT::compileBlock($opcodes, $debugFile);
    }

}