<?php

// Pastikan folder storage di /tmp selalu ada (karena filesystem Vercel berstatus read-only)
$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Arahkan request ke file public/index.php bawaan Laravel
require __DIR__ . '/../public/index.php';

