<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class OperatorStack {
    public ?OperatorStackEntry $tail;

    public function __construct() {
        $this->tail = null;
    }

    public function shouldPopForNextOp(Op $op): bool {
        if ($this->tail === null) {
            return false;
        }
        if ($this->tail->type === Op::TYPE_OP) {
            return false;
        }
        if ($this->tail->isLeftAssociative() && $this->tail->isEqualPrecidence($op)) {
            return true;
        }
        return $this->tail->isLowerPrecidence($op);
    }

    public function isEmpty(): bool {
        return $this->tail === null;
    }

    public function push(Op $op): void {
        $this->tail = new OperatorStackEntry($op, $this->tail);
    }

    public function peek(): ?Op {
        return $this->tail;
    }

    public function pop(): ?Op {
        if (!is_null($this->tail)) {
            $return = $this->tail;
            $this->tail = $this->tail->prev;
            return $return->op;
        }
        return null;
    }
}

class OperatorStackEntry {
    public Op $op;
    public ?OperatorStackEntry $prev;

    public function __construct(Op $op, ?ExprStackEntry $prev) {
        $this->op = $op;
        $this->prev = $prev;
    }
}

class Op {
    const TYPE_OP = 1;
    const TYPE_CONCAT = 2;

    public int $type;

    public function __construct(int $type) {
        $this->type = $type;
    }

    public function toExpr(): Expr {
        switch ($this->type) {
            case self::TYPE_OP:
                throw new \LogicException("Cannot convert ( to an operator");
            case self::TYPE_CONCAT:
                return new Expr(Expr::TYPE_CONCAT);
        }
    }

    public function isLowerPrecidence(Op $op): bool {
        return false;
    }
}