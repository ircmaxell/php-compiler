<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class Context {
    public ?Decl $decl = null;
    public ?Decl $declTail = null;

    public function addDecl(Decl $decl) {
        if (is_null($this->decl)) {
            $this->decl = $this->declTail = $decl;
        } else {
            $this->declTail = $this->declTail->next = $decl;
        }
    }

    
}