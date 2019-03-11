<?php

const ITERATIONS = 5;

$runtimes = [];
foreach ($_ENV as $key => $value) {
    if (substr($key, 0, 4) === 'PHP_') {
        $runtimes[str_replace('_', '.', substr($key, 4))] = $value;
    }
}
ksort($runtimes, SORT_STRING);
if (!isset($runtimes['7.4'])) {
    die("At least a PHP 7.4 runtime must be specified via PHP_7_4\n");
}

$it = new GlobIterator(__DIR__ . '/benchmarks/*.php');
$results = [];

echo "Running " . ITERATIONS . " iterations of each test, and averaging\n";
foreach ($it as $file) {
    echo "Running " . $file->getBasename('.php') . ":\n";
    $results[$file->getBasename('.php')] = bench($file->getPathname(), $runtimes);
}

echo "All Tests Completed, Results: \n\n";
echo "| Test Name          ";
foreach ($runtimes as $name => $path) {
    printf('| %14s (s)', $name);
}
echo "| bin/jit.php (s) | bin/compile.php (s) | compiled time (s) |\n";

echo '|--------------------';
foreach ($runtimes as $name => $path) {
    echo '|' . str_repeat('-', 19);
}
echo "|-----------------|---------------------|-------------------|\n";
foreach ($results as $name => $resultset) {
    printf("| %18s ", $name);
    foreach ($runtimes as $name => $_) {
        printf('|      %12.4f ', $resultset[$name]);
    }
    printf('|    %12.4f ', $resultset['jit']);
    printf('|        %12.4f ', $resultset['aotcompile']);
    printf('|      %12.4f ', $resultset['aot']);
    printf("|\n");
}




function bench(string $file, array $runtimes) {
    echo "Testing each method:\n";
    $result = trim(runDebug($runtimes['7.4'] . ' ' . __DIR__ . '/bin/jit.php', $file));
    run($runtimes['7.4'] . ' ' . __DIR__ . '/bin/compile.php', $file);
    $tmpResult = trim(runDebug(str_replace('.php', '', $file), ''));
    if ($result !== $tmpResult) {
        die("Failure for bin/compile.php, found \"$tmpResult\" but expected \"$result\"\n");
    }
    foreach ($runtimes as $name => $binary) {
        $tmpResult = trim(runDebug($binary, $file));
        if ($result !== $tmpResult) {
            var_dump($result, $tmpResult);
            die("Failure for test $name\n");
        }

    }
    echo "Tests passed for $file, all runtimes agree\n";
    $times = [];
    $start = microtime(true);
    foreach ($runtimes as $name => $binary) {
        run($binary, $file);
        $times[$name] = microtime(true);
    }
    run($runtimes['7.4'] . ' ' . __DIR__ . '/bin/jit.php', $file);
    $times['jit'] = microtime(true);
    run($runtimes['7.4'] . ' ' . __DIR__ . '/bin/compile.php', $file);
    $times['aotcompile'] = microtime(true);
    run(str_replace('.php', '', $file), '');
    $times['aot'] = microtime(true);
    unlink(str_replace('.php', '', $file));

    $results = [];
    foreach ($times as $key => $time) {
        $diff = ($time - $start) / ITERATIONS;
        $start = $time;
        $results[$key] = $diff;
    }
    return $results;
}

function run(string $BIN, string $file) {
    runCmd(escapeshellcmd($BIN) . ' ' . escapeshellarg($file));
}

function runDebug(string $BIN, string $file): string {
    $command = escapeshellcmd($BIN) . ' ' . escapeshellarg($file);
    return _runCmd($command);
}

function runCmd(string $cmd) {
    for ($i = 0; $i < ITERATIONS; $i++) {
        _runCmd($cmd);
    }
}

function _runCmd(string $cmd): string {
    $descriptorSepc = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $pipes = [];
    $proc = proc_open($cmd, $descriptorSepc, $pipes);
    $result = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    proc_close($proc);
    return $result;
}