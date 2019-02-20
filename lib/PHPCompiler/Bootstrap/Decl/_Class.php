<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap\Decl;

class _Class {
    public string $name;
    public string $filename;
    public int $startLine;
    public int $endLine;
    public ?\PHPCompiler\Bootstrap\Decl $decl;
    public ?\PHPCompiler\Bootstrap\Decl $declTail;

    public function __construct() {
        $this->decl = null;
        $this->declTail = null;
    }


    public function addPublicDecl(\PHPCompiler\Bootstrap\Decl $decl) {
        if (is_null($this->decl)) {
            $this->decl = $this->declTail = $decl;
        } else {
            $this->declTail = $this->declTail->next = $decl;
        }
    }

}