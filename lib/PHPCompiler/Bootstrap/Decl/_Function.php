<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap\Decl;

class _Function {
    public string $name;
    public string $filename;
    public int $startLine;
    public int $endLine;
    public ?\PHPCompiler\Bootstrap\Param $params;
    public ?\PHPCompiler\Bootstrap\Param $return;
    public \PHPCompiler\Bootstrap\Block $body;
}

