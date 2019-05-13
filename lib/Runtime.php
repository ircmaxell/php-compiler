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

class Runtime {
    const MODE_EXECUTABLE       = 0b0001;
    const MODE_SHARED_OBJECT    = 0b0010;

    public Compiler $compiler;
    public Parser $parser;
    public Traverser $preprocessor;
    public Traverser $postprocessor;
    public LivenessDetector $detector;
    public Context $context;
    public array $modules = [];
    public int $mode;
    public ?string $debugFile = null;

    public function __construct(int $mode = self::MODE_EXECUTABLE) {
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
        $this->preprocessor->addVisitor(new Visitor\DeadBlockEliminator);
        $this->postprocessor = new Traverser;
        $this->postprocessor->addVisitor(new Visitor\PhiResolver);
        $this->detector = new LivenessDetector;

        $this->typeReconstructor = new TypeReconstructor;

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

    public function load(Module $module): void {
        $this->modules[] = $module;
        $module->init($this);
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

    public function compile(string $entrypoint, string $dir, FileResolver $resolver): void {
        $files = $resolver->resolve($dir);
        var_dump($files);
    }

}
