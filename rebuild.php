<?php

require __DIR__ . '/vendor/autoload.php';

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/lib', FilesystemIterator::CURRENT_AS_PATHNAME),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($it as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    foreach (new GlobIterator($dir . '/*.pre', FilesystemIterator::CURRENT_AS_PATHNAME) as $file) {
        Pre\Plugin\compile($file, preg_replace('(\.pre$)', '.php', $file));
    }
}

