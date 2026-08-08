<?php
declare(strict_types=1);

require_once __DIR__.'/Auth.php';

function view(string $template, array $data = [], bool $auth = true): void
{
    $file = __DIR__.'/Views/'.$template.'.php';
    if (!is_file($file)) {
        throw new RuntimeException("View [{$template}] was not found.");
    }

    $user = current_user();
    $success = flash('success');
    $error = flash('error');
    extract($data, EXTR_SKIP);
    ob_start();
    require $file;
    $content = ob_get_clean();
    require __DIR__.'/Views/layouts/app.php';
}

function render(string $title, string $content, bool $auth = true): void
{
    // Kept only for legacy error rendering while routes move to dedicated views.
    $user = current_user();
    $success = flash('success');
    $error = flash('error');
    require __DIR__.'/Views/layouts/app.php';
}

function asset(string $path): string
{
    return __DIR__.'/public/assets/' . ltrim($path, '/');
}

function render_asset(string $path): string
{
    return config('APP_URL').'/assets/' . ltrim($path, '/');
}

function storage_asset(string $path): string
{
    return rtrim(config('APP_URL'), '/')
        . '/storage/'
        . ltrim($path, '/');
}