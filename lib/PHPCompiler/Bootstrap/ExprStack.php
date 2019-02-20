<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class ExprStack {
    public ?ExprStackEntry $tail;

    public function __construct() {
        $this->tail = null;
    }

    public function flatten(): ?Expr {
        if ($this->tail === null) {
            return null;
        }
        $expr = $this->normalize($this->pop());
        if ($this->tail !== null) {
            // Nesting error, invalid expression
            die("Invalid expression\n");
        }
        return $expr;
    }

    public function normalize(Expr $expr): Expr {
        switch ($expr->getNumberOfArgs()) {
            case 0:
                return $expr;
            case 1:
                if ($this->tail === null) {
                    throw new \LogicException("Unary op requires one arg\n");
                }
                $expr->child1 = $this->normalize($this->pop());
                return $expr;
            case 2:
                if ($this->tail === null) {
                    throw new \LogicException("Binary op requires left arg\n");
                }
                $expr->child1 = $this->normalize($this->pop());
                if ($this->tail === null) {
                    throw new \LogicException("Binary op requires right arg\n");
                }
                $expr->child2 = $this->normalize($this->pop());
                return $expr;
        }
    }

    public function push(Expr $expr): void {
        $this->tail = new ExprStackEntry($expr, $this->tail);
    }

    public function pop(): ?Expr {
        if (!is_null($this->tail)) {
            $return = $this->tail;
            $this->tail = $this->tail->prev;
            return $return->expr;
        }
        return null;
    }
}

class ExprStackEntry {
    public Expr $expr;
    public ?ExprStackEntry $prev;

    public function __construct(Expr $expr, ?ExprStackEntry $prev) {
        $this->expr = $expr;
        $this->prev = $prev;
    }
}