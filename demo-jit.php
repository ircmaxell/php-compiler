<?php

$rawCode = <<<'EOF'
$a = "a";
for ($i = 0; $i < 100000; $i++) {
    $a .= "b";
}
echo $a;
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

echo "\n\nExecuting JIT Locally With Null Check\n\n";

if (!is_null($block->handler)) {
    ($block->handler->callback)();
}

$times["local null-safe jit execute"] = microtime(true);

flush();

$times["local null-safe jit flush"] = microtime(true);

echo "\n\nExecuting JIT Locally Without Null Check\n\n";

($block->handler->callback)();

$times["local jit execute"] = microtime(true);

flush();

$times["local jit flush"] = microtime(true);

echo "\n\nExecuting JIT\n\n";

$runtime->run($block);

$times["jit execute"] = microtime(true);

flush();

$times["jit flush"] = microtime(true);

echo "\n\nExecuting Eval\n\n";

eval($rawCode);

$times["eval execute"] = microtime(true);

flush();

$times["eval flush"] = microtime(true);

echo "\n\nTimers:\n";

$start = array_shift($times);
foreach ($times as $key => $time) {
    echo "  $key => " . ($time - $start) . "\n";
    $start = $time;
}
echo "\n\n";