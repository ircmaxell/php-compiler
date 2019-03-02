<?php

$rawCode = <<<'EOF'
$a = "test";
for ($i = 0; $i < 100; $i++) {
    $a .= $i;
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

// echo "\n\nExecuting VM\n\n";

// $runtime->run($block);

// $times["vm execute"] = microtime(true);

// flush();

// $times["vm flush"] = microtime(true);

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
    printf("  %30s => %02.8F\n", $key, $time - $start);
    $start = $time;
}
echo "\n\n";