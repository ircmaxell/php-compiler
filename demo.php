<?php

use PhpParser\ParserFactory;
set_time_limit(2);

$code = <<<'EOF'
<?php

function foo(int $offset) {
	$a = 1;
	for ($i = 0; $i < $offset; $i++) {
		$a += $offset;
	}
	return $a;
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


$compiler = new PHPCompiler\Backend\PHP7\PECL;
$files = $compiler->compile($blocks);
var_dump($files);