<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class Block {
    
    public ?Block $next;

    public ?Expr $expr;
    public ?Expr $exprTail;

    public function __construct() {
        $this->next = null;
        $this->expr = null;
        $this->exprTail = null;
    }

    public function addExpression(Expr $expr): void {
        if (is_null($this->expr)) {
            $this->expr = $this->exprTail = $expr;
        } else {
            $this->exprTail = $this->exprTail->next = $expr;
        }
    }

}
