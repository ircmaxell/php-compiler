<?php

use PhpParser\ParserFactory;
set_time_limit(2);

$code = <<<'EOF'
<?php

class Foo {
    /**
     * @var int
     */
    public $int;

    /**
    * @var string
    */
    public $string;
    
    /**
    * @var double
    */
    public $double;
}
EOF;

require 'vendor/autoload.php';

$astTraverser = new PhpParser\NodeTraverser;
$astTraverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);
$parser = new PHPCfg\Parser((new ParserFactory)->create(ParserFactory::PREFER_PHP7), $astTraverser);

$traverser = new PHPCfg\Traverser;
$traverser->addVisitor(new PHPCfg\Visitor\Simplifier);

$typeReconstructor = new PHPTypes\TypeReconstructor;
$dumper = new PHPCfg\Printer\Text();

$block = $parser->parse($code, __FILE__);
$traverser->traverse($block);
$state = new PHPTypes\State([$block]);
$typeReconstructor->resolve($state);
$blocks = $state->blocks;

$optimizer = new PHPOptimizer\Optimizer;
//$blocks = $optimizer->optimize($blocks);

function rmdir_recursive($dir) {
    $it = new RecursiveDirectoryIterator($dir);
    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($it as $file) {
        if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
        if ($file->isDir()) rmdir($file->getPathname());
        else unlink($file->getPathname());
    }
    rmdir($dir);
}

rmdir_recursive(__DIR__ . "/ext");
mkdir(__DIR__ . "/ext");

$compiler = new PHPCompiler\Backend\PHP7\PECL;
$files = $compiler->compile($blocks);

foreach ($files as $filename => $content) {
    file_put_contents(__DIR__ . "/ext/$filename", $content);
}


