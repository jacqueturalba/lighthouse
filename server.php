<?php

declare(strict_types=1);

$public = __DIR__ . DIRECTORY_SEPARATOR . 'public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
);

$file = $public . $uri;

if ($uri !== '/' && is_file($file)) {

    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'mjs'  => 'application/javascript',
        'html' => 'text/html',
        'htm'  => 'text/html',
        'json' => 'application/json',

        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',

        'mp4'  => 'video/mp4',
        'webm' => 'video/webm',

        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
    ];

    $mime = $mimeTypes[$extension] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($file));
    header('X-Content-Type-Options: nosniff');

    readfile($file);
    exit;
}

require $public . DIRECTORY_SEPARATOR . 'index.php';