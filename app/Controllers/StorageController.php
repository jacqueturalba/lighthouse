<?php
declare(strict_types=1);

final class StorageController
{
    public function show(array $params): void
    {
        require_auth();

        $parts = [
            $params['type'] ?? '',
            $params['folder'] ?? '',
            $params['file'] ?? '',
        ];

        foreach ($parts as $part) {
            if (
                $part === '' ||
                basename($part) !== $part ||
                str_contains($part, "\0")
            ) {
                http_response_code(404);
                return;
            }
        }

        $root = realpath(
            config('STORAGE_PATH', dirname(__DIR__, 2) . '/storage')
        );

        if (!$root) {
            http_response_code(404);
            return;
        }

        $file = realpath(
            $root . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts)
        );

        if (
            !$file ||
            !str_starts_with(
                $file,
                $root . DIRECTORY_SEPARATOR
            ) ||
            !is_file($file) ||
            !is_readable($file)
        ) {
            http_response_code(404);
            return;
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file)
            ?: 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($file));
        header('X-Content-Type-Options: nosniff');

        readfile($file);
        exit;
    }
}