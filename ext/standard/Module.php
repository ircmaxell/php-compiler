<?php declare(strict_types=1);

namespace PHPCompiler\ext\standard;

use PHPCompiler\ModuleAbstract;

class Module extends ModuleAbstract {

    public function getFunctions(): array {
        return [
            new var_dump,
            new strlen,
        ];
    }

}