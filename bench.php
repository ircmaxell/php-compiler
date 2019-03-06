<?php

const ITERATIONS = 5;

$it = new GlobIterator(__DIR__ . '/benchmarks/*.php');
$results = [];

echo "Running " . ITERATIONS . " iterations of each test, and averaging\n";
foreach ($it as $file) {
    echo "Running " . $file->getBasename('.php') . ":\n";
    $results[$file->getBasename('.php')] = bench($file->getPathname());
}

echo "All Tests Completed, Results: \n\n";
echo "| Test Name          | Native PHP Time (s) |                JIT Time (s) | Compilation Time (s) |                 Compiled Time (s) |\n";
echo "|--------------------|---------------------|-----------------------------|----------------------|-----------------------------------|\n";
foreach ($results as $name => $resultset) {
    printf(
        "| %18s |            %2.6F |   %2.6F (%6.2F%% faster) |             %2.6F |      %2.6F (%9.2F%% faster) |\n", 
        $name, 
        $resultset['native'], 
        $resultset['jit'],
        100 * (($resultset['native'] / $resultset['jit']) - 1), 
        $resultset['aotcompile'], 
        $resultset['aot'],
        100 * (($resultset['native'] / $resultset['aot']) - 1)
    );
}




function bench(string $file) {
    $times = [];
    $start = microtime(true);
    run('', $file);
    $times['native'] = microtime(true);
    run('bin/jit.php', $file);
    $times['jit'] = microtime(true);
    run('bin/compile.php', $file);
    $times['aotcompile'] = microtime(true);
    runCmd(str_replace('.php', '', $file));
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
    if (!isset($_SERVER['_'])) {
        $PHP = 'php';
    } elseif ($_SERVER['_'][0] === '/') {
        $PHP = $_SERVER['_'];
    } else {
        $PHP = realpath($_SERVER['PWD'] . '/' . $_SERVER['_']);
    }
    runCmd("$PHP {$BIN} " . escapeshellarg($file));
}

function runCmd(string $cmd) {
    for ($i = 0; $i < ITERATIONS; $i++) {
        _runCmd($cmd);
    }
}

function _runCmd(string $cmd) {
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
}