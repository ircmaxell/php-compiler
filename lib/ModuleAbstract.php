<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;
use PHPCfg\Script;
use PHPCfg\Func as CfgFunc;

abstract class ModuleAbstract implements Module {
    protected Runtime $runtime;

    public function getName(): string {
        return str_replace('\\', '_', get_class($this));
    }

    public function getFunctions(): array {
        return [];
    }

    public function init(Runtime $runtime): void {
        $this->runtime = $runtime;
    }

    public function shutdown(): void {
        
    }

    protected function parseAndCompileFunction(string $name, string $filename): Func {
        $script = $this->runtime->parse(file_get_contents($filename), $filename);
        $func = $this->findFunction($name, $script);
        return $this->runtime->compileFunc($name, $func);
    }

    protected function findFunction(string $name, Script $script): CfgFunc {
        foreach ($script->functions as $func) {
            $parts = explode('\\', $func->name);
            if ($func->name === $name) {
                return $func;
            } elseif (end($parts) === $name) {
                return $func;
            }
        }
        throw new \LogicException('Could not find function named ' . $name);
    }

}