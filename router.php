<?php
// router.php

// This file is used by the PHP built-in server to route requests

$requested = $_SERVER["REQUEST_URI"];
$path = parse_url($requested, PHP_URL_PATH);

// Handle root directory - redirect to index.php
if ($path === '/') {
    $path = '/index.php';
}

$file = __DIR__ . '/public' . $path;

if (php_sapi_name() === 'cli-server') {
    if (is_file($file)) {
        return false;
    } else {
        // Serve custom 404 page
        http_response_code(404);
        require __DIR__ . '/public/404.php';
        return true;
    }
}
