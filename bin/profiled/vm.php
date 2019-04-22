<?php declare(strict_types=1);

sample_prof_start(50);              // start profiler with 50 usec interval
require __DIR__ . '/../vm.php';    // run script here
sample_prof_end();                  // disable profiler
$data = sample_prof_get_data();     // retrieve profiling data

foreach ($data as $file => $lines) {
    echo "In file $file:\n";
    foreach ($lines as $line => $hits) {
        echo "Line $line hit $hits times.\n";
    }
}