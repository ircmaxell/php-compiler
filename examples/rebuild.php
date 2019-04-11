<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

echo "Rebuilding Examples\n";

$benchmarks = <<<HERE
|         Example Name |      Native PHP |      bin/vm.php |     bin/jit.php | bin/compile.php |      ./compiled |
|----------------------|-----------------|-----------------|-----------------|-----------------|-----------------|
HERE;

$it = new DirectoryIterator(__DIR__);
foreach ($it as $file) {
    if ($file->isFile()) {
        continue;
    }
    $example = $file->getPathname().'/example.php';
    if (file_exists($example)) {
        echo ' - Building Example '.$file->getBasename()."\n";

        $cmd = escapeshellcmd(\PHP_BINARY).' '.escapeshellarg(__DIR__.'/../bin/jit.php').' -y '.escapeshellarg($example);
        ob_start();
        passthru($cmd);
        file_put_contents($file->getPathname().'/example.output', ob_get_clean());
        $benchmarks .= "\n".benchmark($example);
    }
}

$readme = file_get_contents(__DIR__.'/README.md');

$readme = preg_replace('((<!-- benchmark table start -->)(.*)(<!-- benchmark table end -->))ims', "\$1\n\n".$benchmarks."\n\$3", $readme);

file_put_contents(__DIR__.'/README.md', $readme);

echo "Done\n";

function benchmark(string $example): string
{
    $iterations = 10;
    echo "Benchmarking ${example}\n";
    $start = microtime(true);
    $timers = [];
    $cmd = escapeshellcmd(\PHP_BINARY).' '.escapeshellarg($example);
    for ($i = 0; $i < $iterations; ++$i) {
        ob_start();
        exec($cmd);
        ob_end_clean();
    }
    $timers['native'] = microtime(true);
    $cmd = escapeshellcmd(\PHP_BINARY).' '.escapeshellarg(__DIR__.'/../bin/vm.php').' '.escapeshellarg($example);
    for ($i = 0; $i < $iterations; ++$i) {
        ob_start();
        exec($cmd);
        ob_end_clean();
    }
    $timers['vm'] = microtime(true);
    $cmd = escapeshellcmd(\PHP_BINARY).' '.escapeshellarg(__DIR__.'/../bin/jit.php').' '.escapeshellarg($example);
    for ($i = 0; $i < $iterations; ++$i) {
        ob_start();
        exec($cmd);
        ob_end_clean();
    }
    $timers['jit'] = microtime(true);
    $cmd = escapeshellcmd(\PHP_BINARY).' '.escapeshellarg(__DIR__.'/../bin/compile.php').' '.escapeshellarg($example);
    for ($i = 0; $i < $iterations; ++$i) {
        ob_start();
        exec($cmd);
        ob_end_clean();
    }
    $timers['compile'] = microtime(true);
    $cmd = escapeshellcmd(str_replace('.php', '', $example));
    for ($i = 0; $i < $iterations; ++$i) {
        ob_start();
        exec($cmd);
        ob_end_clean();
    }
    $timers['compiled-result'] = microtime(true);
    $times = [];
    $averages = [];
    foreach ($timers as $name => $time) {
        $times[$name] = $time - $start;
        $averages[$name] = $times[$name] / $iterations;
        $start = $time;
    }
    $result = sprintf('| %20s |', basename(dirname($example)));
    foreach ($averages as $name => $average) {
        $result .= sprintf('         %0.5f |', $average);
    }

    return $result;
}
