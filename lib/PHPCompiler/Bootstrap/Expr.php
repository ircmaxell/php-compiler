<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class Expr {
    const TYPE_NONE = 0;
    const TYPE_VAR = 1;
    const TYPE_STRING = 2;
    const TYPE_INTEGER = 3;
    const TYPE_BOOL = 4;
    const TYPE_FUNCTION_CALL = 5;
    const TYPE_ASSIGN = 6;

    const MIN_UNARY = 100;

    const MIN_BINARY = 200;
    const TYPE_CONCAT = 201;

    public int $type;

    public string $string;
    public int $integer;
    public bool $bool;

    public ?Expr $child1;
    public ?Expr $child2;
    public ?Expr $child3;
    public ?Expr $child4;

    public ?Expr $next;
    

    public function __construct(int $type) {
        $this->type = $type;
        $this->next = null;
        $this->child1 = null;
        $this->child2 = null;
        $this->child3 = null;
        $this->child4 = null;
    }

    public function isConst(): bool {
        switch ($this->type) {
            case self::TYPE_STRING:
            case self::TYPE_INTEGER:
            case self::TYPE_BOOL:
                return true;
        }
        return false;
    }

    public function getNumberOfArgs(): int {
        if ($this->type < self::MIN_UNARY) {
            return 0;
        } elseif ($this->type < self::MIN_BINARY) {
            return 1;
        }
        return 2;
    }
}
