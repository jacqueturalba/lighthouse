<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/Auth.php';
require_once dirname(__DIR__).'/Services/PromotionKitRequestService.php';
require_once dirname(__DIR__).'/Models/PromotionKitDownload.php';

final class PromotionKitRequestController
{
    private PromotionKitRequestService $service;

    public function __construct()
    {
        $this->service = new PromotionKitRequestService();
    }

    public function store(array $params): void
    {
        $user = require_auth(); csrf();
        flash('success', $this->service->request((int) $params['id'], (int) $user['id']));
        redirect('/promotion-kits/'.(int) $params['id']);
    }

    public function approve(array $params): void
    {
        $user = require_super_admin(); csrf();
        flash('success', $this->service->review((int) $params['id'], 'approved', $user, null));
        redirect('/promotion-kit-requests');
    }

    public function disapprove(array $params): void
    {
        $user = require_super_admin(); csrf();
        $message = $this->service->review((int) $params['id'], 'disapproved', $user, $_POST['reason'] ?? '');
        flash(str_contains($message, 'required') ? 'error' : 'success', $message);
        redirect('/promotion-kit-requests');
    }

    public function download(array $params): void
    {
        $user = require_auth();
        $row = PromotionKitRequest::downloadable((int) $params['id'], (int) $user['id']);
        if (!$row) { http_response_code(403); render('Download unavailable', '<p>Your request must be approved and the kit must be active.</p>'); exit; }
        $root = realpath(config('STORAGE_PATH', dirname(__DIR__, 2).'/storage'));
        $file = $root ? realpath($root.DIRECTORY_SEPARATOR.ltrim($row['file_path'], '/\\')) : false;
        if (!$root || !$file || !str_starts_with($file, $root.DIRECTORY_SEPARATOR) || !is_file($file) || !is_readable($file)) { http_response_code(404); render('File unavailable', '<p>The promotion kit file could not be found.</p>'); exit; }
        PromotionKitDownload::record((int) $params['id'], (int) $user['id'], (int) $row['id']);
        log_event('promotion_kit_downloaded', ['kit_id'=>(int)$params['id'], 'user_id'=>(int)$user['id'], 'request_id'=>(int)$row['id']]);
        header('Content-Type: '.($row['mime_type'] ?: 'application/octet-stream'));
        header('Content-Length: '.filesize($file));
        header('Content-Disposition: attachment; filename="'.str_replace(['"', "\r", "\n"], '', basename($row['original_file_name'])).'"');
        header('X-Content-Type-Options: nosniff');
        readfile($file); exit;
    }

    public function archive(array $params): void
    {
        require_super_admin(); csrf();
        $kit = PromotionKit::find((int) $params['id']);
        if ($kit) { PromotionKit::archive((int) $params['id']); flash('success', 'Promotion kit archived.'); }
        redirect('/promotion-kits');
    }

    public function upload(array $params = []): void
    {
        $user = require_super_admin(); csrf();
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $file = $_FILES['file'] ?? null;
        $errors = [];
        if ($title === '' || mb_strlen($title) > 150) $errors[] = 'Enter a title up to 150 characters.';
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) $errors[] = 'Choose a file to upload.';
        $allowed = ['zip'=>'application/zip', 'pdf'=>'application/pdf', 'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'pptx'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation'];
        $extension = $file ? strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION)) : '';
        $mime = $file && is_uploaded_file($file['tmp_name']) ? (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) : '';
        if ($file && (!isset($allowed[$extension]) || $mime !== $allowed[$extension])) $errors[] = 'Only valid ZIP, PDF, DOCX, or PPTX files are allowed.';
        if ($file && (int)$file['size'] > 50 * 1024 * 1024) $errors[] = 'Files must be 50 MB or smaller.';
        if ($errors) { flash('error', implode(' ', $errors)); redirect('/promotion-kit-upload'); }
        $root = config('STORAGE_PATH', dirname(__DIR__, 2).'/storage');
        $folder = 'promotion-kits'; $directory = rtrim($root, '/\\').DIRECTORY_SEPARATOR.$folder;
        if (!is_dir($directory)) mkdir($directory, 0700, true);
        $stored = bin2hex(random_bytes(20)).'.'.$extension; $path = $folder.'/'.$stored;
        if (!move_uploaded_file($file['tmp_name'], $directory.DIRECTORY_SEPARATOR.$stored)) { flash('error', 'The file could not be stored.'); redirect('/promotion-kit-upload'); }
        try { PromotionKit::create(['title'=>$title,'description'=>$description,'original'=>$file['name'],'stored'=>$stored,'path'=>$path,'extension'=>$extension,'mime'=>$mime,'size'=>(int)$file['size'],'cover'=>null,'user_id'=>(int)$user['id']]); }
        catch (Throwable $e) { @unlink($directory.DIRECTORY_SEPARATOR.$stored); flash('error', 'The promotion kit could not be saved.'); redirect('/promotion-kit-upload'); }
        flash('success', 'Promotion kit uploaded.'); redirect('/promotion-kits');
    }
}
