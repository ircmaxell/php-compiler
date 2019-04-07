<?php declare(strict_types=1);

namespace PHPCompiler\ext\standard;

use PHPCfg\Func as CfgFunc;
use PHPCfg\Script;
use PHPCompiler\Func;
use PHPCompiler\ModuleAbstract;

class Module extends ModuleAbstract {

    public function getFunctions(): array {
        return [
            //$this->parseAndCompileFunction('str_repeat', __DIR__ . '/str_repeat.php'),
        ];
    }

}