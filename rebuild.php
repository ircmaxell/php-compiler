<?php

require __DIR__ . '/vendor/autoload.php';

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::CURRENT_AS_PATHNAME),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($it as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    if (substr($dir, -1) === '.') {
        continue; 
    }
    if (substr($dir, -6) === 'vendor') {
        // ensure we don't delve into the vendor directory
        continue;
    }
    foreach (new GlobIterator($dir . '/*.pre', FilesystemIterator::CURRENT_AS_PATHNAME) as $file) {
        echo "Compiling $file\n";
        Pre\Plugin\compile($file, preg_replace('(\.pre$)', '.php', $file));
    }
}

