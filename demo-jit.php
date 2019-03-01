<?php

use PhpParser\ParserFactory;

$rawCode = <<<'EOF'
$test = 0;
for ($i = 0; $i < 1000000; $i++) {
    $test += $i;
}
$test += 2;
echo "hi";
EOF;
$code = '<?php ' . $rawCode;

require 'vendor/autoload.php';

$times = ["start" => microtime(true)];

$runtime = new PHPCompiler\Backend\VM\Runtime;

$times["Setup"] = microtime(true);

$block = $runtime->parseAndCompile($code, __FILE__);

$times["parseAndCompile"] = microtime(true);

$runtime->jit($block, __DIR__ . '/result');

$times["jit compile"] = microtime(true); 

echo "\n\nExecuting JIT\n\n";

$runtime->run($block);

flush();

$times["jit execute"] = microtime(true);

echo "\n\nExecuting Eval\n\n";

eval($rawCode);

flush();

$times["eval execute"] = microtime(true);

echo "\n\nTimers:\n";

$start = array_shift($times);
foreach ($times as $key => $time) {
    echo "  $key => " . ($time - $start) . "\n";
    $start = $time;
}
echo "\n\n";