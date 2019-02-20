<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap\Decl;

class Property {
    public string $name;
    public string $type;
    public string $filename;
    public int $startLine;
    public int $endLine;
}

