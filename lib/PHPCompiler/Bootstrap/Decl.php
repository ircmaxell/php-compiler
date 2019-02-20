<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class Decl {
    const TYPE_NONE = 0;
    const TYPE_CLASS = 1;
    const TYPE_PROPERTY = 2;
    const TYPE_FUNCTION = 3;
    const TYPE_METHOD = 4;

    public int $type;
    public Decl\_Class $class;
    public Decl\_Function $function;
    public Decl\Method $method;
    public Decl\Property $property;

    public ?Decl $next = null;

    public function __construct(int $type) {
        $this->type = $type;
    }

    public function finish(Token $node): void {
        switch ($this->type) {
            case self::TYPE_CLASS:
                $this->class->endLine = $node->line;
                break;
            case self::TYPE_FUNCTION:
                $this->function->endLine = $node->line;
                break;
            case self::TYPE_METHOD:
                $this->method->endLine = $node->line;
                break;
            case self::TYPE_PROPERTY:
                $this->property->endLine = $node->line;
                break;
        }
    }

    public static function _class(Token $node): self {
        $self = new self(self::TYPE_CLASS);
        $self->class = new Decl\_Class;
        $self->class->filename = $node->filename;
        $self->class->startLine = $node->line;
        return $self;
    }

    public static function _function(Token $node): self {
        $self = new self(self::TYPE_FUNCTION);
        $self->function = new Decl\_Function;
        $self->function->filename = $node->filename;
        $self->function->startLine = $node->line;
        return $self;
    }

    public static function method(Token $node): self {
        $self = new self(self::TYPE_METHOD);
        $self->method = new Decl\Method;
        $self->method->filename = $node->filename;
        $self->method->startLine = $node->line;
        return $self;
    }

    public static function property(Token $node): self {
        $self = new self(self::TYPE_PROPERTY);
        $self->property = new Decl\Property;
        $self->property->filename = $node->filename;
        $self->property->startLine = $node->line;
        return $self;
    }
}
