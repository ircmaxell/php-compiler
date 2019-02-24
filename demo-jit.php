<?php

use PhpParser\ParserFactory;

$rawCode = <<<'EOF'
$a = "Hello";
$a = "$a World\n";
echo $a;
EOF;
$code = '<?php ' . $rawCode;

require 'vendor/autoload.php';

function dump($obj, int $depth=3) {
    if ($depth <= 0) {
        echo str_repeat('  ', 4) . "<truncated...>\n";
        return;
    }
    if (is_array($obj)) {
        echo str_repeat('  ', abs($depth - 3)) . "[\n";
        foreach ($obj as $key => $element) {
            echo str_repeat('  ',abs($depth - 4)) . $key . ":\n";
            dump($element, $depth - 1);
            echo "\n";
        }
        echo str_repeat('  ', abs($depth - 3)) . "];\n";
        return;
    } elseif (is_null($obj)) {
        return str_repeat('  ', abs($depth - 3)) . "null\n";
    } elseif (!is_object($obj)) {
        return str_repeat('  ', abs($depth - 3)) . print_r($obj, true) . "\n";
    }
    echo str_repeat('  ', abs($depth - 3)) . 'object<' . get_class($obj) . "> {\n";
    $r = new \ReflectionObject($obj);
    foreach ($r->getProperties() as $prop) {
        echo str_repeat('  ', abs($depth - 4)) . '$' . $prop->getName() . "->\n";
        $prop->setAccessible(true);
        dump($prop->getValue($obj), $depth - 1);
    }
    echo str_repeat('  ', abs($depth - 3)) . "}\n";
}


$times = [];

$times['start'] = microtime(true);
$astTraverser = new PhpParser\NodeTraverser;
$astTraverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);
$parser = new PHPCfg\Parser((new ParserFactory)->create(ParserFactory::ONLY_PHP7), $astTraverser);

$traverser = new PHPCfg\Traverser;
$traverser->addVisitor(new PHPCfg\Visitor\Simplifier);

$typeReconstructor = new PHPTypes\TypeReconstructor;
$dumper = new PHPCfg\Printer\Text();
//$optimizer = new PHPOptimizer\Optimizer;
$compiler = new PHPCompiler\Backend\VM\Compiler;
$compileContext = new PHPCompiler\Backend\VM\Context;

$times['Initialize Libraries'] = microtime(true);

$script = $parser->parse($code, __FILE__);
$times['Parse'] = microtime(true);

$traverser->traverse($script);
$times['Traverse CFG'] = microtime(true);

$state = new PHPTypes\State($script);
$typeReconstructor->resolve($state);
$times['Reconstruct Types'] = microtime(true);

//$blocks = $optimizer->optimize($blocks);

$opcodes = $compiler->compile($script);
$times['Compile'] = microtime(true);


PHPCompiler\Backend\VM\JIT::compileBlock($opcodes, __DIR__ . '/result');
$times['JIT Compile'] = microtime(true);


echo $dumper->printScript($script);
$times['Dump CFG'] = microtime(true);

echo "\n\nEval Output:\n\n";
eval($rawCode);
$times['Eval Code'] = microtime(true);

echo "\n\nCompiled Output\n\n";
PHPCompiler\Backend\VM\VM::run($opcodes, $compileContext);
//($opcodes->handler->callback)();
$times['Run in Compiled'] = microtime(true);

unset($opcodes->handler);
//echo "";
echo "\n\nVM Output\n\n";

//PHPCompiler\Backend\VM\VM::run($opcodes, $compileContext);
$times['Run in VM'] = microtime(true);

echo "\n\nTimers:\n";
$start = array_shift($times);
foreach ($times as $key => $time) {
    echo "  $key: " . ($time - $start) . "\n";
    $start = $time;
}
