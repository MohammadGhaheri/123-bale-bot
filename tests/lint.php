<?php

declare(strict_types=1);

$roots = [__DIR__ . '/../src', __DIR__ . '/../examples', __DIR__];
$files = [];

foreach ($roots as $root) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
}

$failed = false;
foreach ($files as $file) {
    $command = PHP_BINARY . ' -l ' . escapeshellarg($file);
    exec($command, $output, $code);
    if ($code !== 0) {
        $failed = true;
        echo implode("\n", $output) . "\n";
    }
}

if ($failed) {
    exit(1);
}

echo count($files) . " PHP files passed lint.\n";
