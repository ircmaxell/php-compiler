<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class Param {
    public string $name;
    public string $type;
    public ?\gcc_jit_param_ptr $gccParam;

    public ?Param $next;

    public function __construct() {
        $this->next = null;
    }

}
