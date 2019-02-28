<?php

use PhpParser\ParserFactory;

$rawCode = <<<'EOF'
$a = "";
for ($i = 0; $i < 10; $i++) {
    $a = $a . "a";
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

$runtime->run($block);

$times["execute"] = microtime(true);

flush();

echo "\n\nTimers:\n";

$start = array_shift($times);
foreach ($times as $key => $time) {
    echo "  $key => " . ($time - $start) . "\n";
    $start = $time;
}
echo "\n\n";