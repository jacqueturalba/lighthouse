<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/Auth.php';
require_once dirname(__DIR__).'/Services/PromotionKitRequestService.php';
require_once dirname(__DIR__).'/Models/PromotionKitDownload.php';
require_once dirname(__DIR__).'/Models/UploadJob.php';

final class PromotionKitRequestController
{
    private PromotionKitRequestService $service;

    public function __construct()
    {
        $this->service = new PromotionKitRequestService();
    }

    public function store(array $params): void
    {
        $user = require_auth(); 
        csrf();
        flash('success', $this->service->request((int) $params['id'], (int) $user['id']));
        redirect('/promotion-kits/'.(int) $params['id']);
    }

    public function approve(array $params): void
    {
        $user = require_super_admin(); 
        csrf();
        flash('success', $this->service->review((int) $params['id'], 'approved', $user, null));
        redirect('/promotion-kit-requests');
    }

    public function disapprove(array $params): void
    {
        $user = require_super_admin(); 
        csrf();
        $message = $this->service->review((int) $params['id'], 'disapproved', $user, $_POST['reason'] ?? '');
        flash(str_contains($message, 'required') ? 'error' : 'success', $message);
        redirect('/promotion-kit-requests');
    }

    public function download(array $params): void
    {
        $user = require_auth();

        $kitId = (int) ($params['id'] ?? 0);
        $userId = (int) $user['id'];

        if ($kitId <= 0) {
            http_response_code(404);
            render(
                'File unavailable',
                '<p>The promotion kit file is not available for download.</p>'
            );
            exit;
        }

        $kit = PromotionKit::find($kitId);

        if (!$kit || $kit['status'] !== 'active') {
            http_response_code(404);
            render(
                'File unavailable',
                '<p>The promotion kit file is not available for download.</p>'
            );
            exit;
        }

        /*
        * Find the user's existing access/request record.
        */
        $request = PromotionKitRequest::forUserAndKit(
            $kitId,
            $userId
        );

        /*
        * Available to All:
        * Automatically create an approved access record
        * if the user doesn't already have one.
        */
        if ($kit['access_type'] === 'all') {

            if (!$request) {
                PromotionKitRequest::createAutoApproved(
                    $kitId,
                    $userId
                );

                $request = PromotionKitRequest::forUserAndKit(
                    $kitId,
                    $userId
                );
            }

        /*
        * Request Access:
        * User must have an approved request.
        */
        } elseif ($kit['access_type'] === 'request') {

            if (!$request || $request['status'] !== 'approved') {
                http_response_code(403);

                render(
                    'Download unavailable',
                    '<p>Your request must be approved and the kit must be active.</p>'
                );

                exit;
            }

        } else {

            // Unknown access type — fail closed.
            http_response_code(403);

            render(
                'Download unavailable',
                '<p>This promotion kit has an invalid access setting.</p>'
            );

            exit;
        }

        /*
        * At this point, both access types should have
        * an approved request/access record.
        */
        if (!$request || $request['status'] !== 'approved') {
            http_response_code(403);

            render(
                'Download unavailable',
                '<p>Download access could not be verified.</p>'
            );

            exit;
        }

        /*
        * Get the actual downloadable file.
        */
        $row = PromotionKitRequest::downloadable(
            $kitId,
            $userId
        );

        if (!$row) {
            http_response_code(403);

            render(
                'Download unavailable',
                '<p>Your request must be approved and the kit must be active.</p>'
            );

            exit;
        }

        /* Resolve every attached file safely before recording or sending it. */
        $root = realpath(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage');
        $attached = $kit['media'] ?? [];
        $attached[] = [
            'file_path' => $row['file_path'],
            'original_file_name' => $row['original_file_name'],
            'mime_type' => $row['mime_type'],
        ];

        $files = [];
        $seenPaths = [];
        foreach ($attached as $item) {
            $path = $root ? realpath($root . DIRECTORY_SEPARATOR . ltrim($item['file_path'], '/\\')) : false;
            if (!$path || !str_starts_with($path, $root . DIRECTORY_SEPARATOR) || !is_file($path) || !is_readable($path)) {
                http_response_code(404);
                render('File unavailable', '<p>The promotion kit file could not be found.</p>');
                exit;
            }
            if (isset($seenPaths[$path])) continue;
            $seenPaths[$path] = true;
            $files[] = [
                'path' => $path,
                'name' => basename(str_replace('\\', '/', (string)$item['original_file_name'])),
                'mime' => (string)($item['mime_type'] ?? ''),
            ];
        }

        if (!$files) {
            http_response_code(404);
            render('File unavailable', '<p>The promotion kit file could not be found.</p>');
            exit;
        }

        $downloadFile = $files[0]['path'];
        $downloadName = $files[0]['name'];
        $downloadMime = $files[0]['mime'] ?: 'application/octet-stream';
        $temporaryZip = null;
        if (count($files) > 1) {
            if (!class_exists(ZipArchive::class)) {
                http_response_code(500);
                render('Download unavailable', '<p>A ZIP archive could not be created.</p>');
                exit;
            }
            $temporaryZip = tempnam(sys_get_temp_dir(), 'lh-kit-');
            $zip = new ZipArchive();
            if ($temporaryZip === false || $zip->open($temporaryZip, ZipArchive::OVERWRITE) !== true) {
                if ($temporaryZip) @unlink($temporaryZip);
                http_response_code(500);
                render('Download unavailable', '<p>A ZIP archive could not be created.</p>');
                exit;
            }
            $entryNames = [];
            foreach ($files as $item) {
                $entry = $item['name'] !== '' ? $item['name'] : basename($item['path']);
                $base = pathinfo($entry, PATHINFO_FILENAME);
                $extension = pathinfo($entry, PATHINFO_EXTENSION);
                $suffix = 2;
                while (isset($entryNames[strtolower($entry)])) {
                    $entry = $base . ' (' . $suffix++ . ')' . ($extension !== '' ? '.' . $extension : '');
                }
                $entryNames[strtolower($entry)] = true;
                if (!$zip->addFile($item['path'], $entry)) {
                    $zip->close();
                    @unlink($temporaryZip);
                    http_response_code(500);
                    render('Download unavailable', '<p>A ZIP archive could not be created.</p>');
                    exit;
                }
            }
            if (!$zip->close()) {
                @unlink($temporaryZip);
                http_response_code(500);
                render('Download unavailable', '<p>A ZIP archive could not be created.</p>');
                exit;
            }
            $downloadFile = $temporaryZip;
            $downloadName = 'promotion-kit-' . $kitId . '.zip';
            $downloadMime = 'application/zip';
        }

        /*
        * Record the download against the access/request record.
        */
        PromotionKitDownload::record(
            $kitId,
            $userId,
            (int) $request['id']
        );

        log_event(
            'promotion_kit_downloaded',
            [
                'kit_id' => $kitId,
                'user_id' => $userId,
                'request_id' => (int) $request['id'],
            ]
        );

        /*
        * Send the file.
        */
        header('Content-Type: ' . $downloadMime);
        header('Content-Length: ' . filesize($downloadFile));
        header('Content-Disposition: attachment; filename="' . str_replace(['"', "\r", "\n"], '', $downloadName) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($downloadFile);
        if ($temporaryZip) @unlink($temporaryZip);
        exit;
    }

    public function archive(array $params): void
    {
        require_super_admin(); 
        csrf();
        $kit = PromotionKit::find((int) $params['id']);
        if ($kit) { 
            PromotionKit::archive((int) $params['id']); 
            flash('success', 'Promotion kit archived.'); 
        }
        redirect('/promotion-kits');
    }

    public function upload(array $params = []): void
    {
        $user = require_super_admin();
        csrf();
        $async = (($_POST['ajax'] ?? '') === '1');
        $jobId = (int) ($_POST['upload_job_id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $fileInput = $_FILES['file'] ?? null;
        $files = [];
        if ($fileInput && is_array($fileInput['name'] ?? null)) {
            foreach ($fileInput['name'] as $index => $name) {
                $files[] = [
                    'name' => $name,
                    'type' => $fileInput['type'][$index] ?? '',
                    'tmp_name' => $fileInput['tmp_name'][$index] ?? '',
                    'error' => $fileInput['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $fileInput['size'][$index] ?? 0,
                ];
            }
        } elseif ($fileInput && ($fileInput['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $files[] = $fileInput;
        }
        $errors = [];
        
        if ($title === '' || mb_strlen($title) > 150) $errors[] = 'Enter a title up to 150 characters.';

        if (!$files) $errors[] = 'Choose a file to upload.';
        if (count($files) > 10) $errors[] = 'Upload up to 10 images at once.';
        
        $allowed = [
            'zip'  => [
                'application/zip',
                'application/x-zip-compressed',
            ],

            'pdf'  => [
                'application/pdf',
            ],

            'docx' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],

            'pptx' => [
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ],

            'jpg'  => [
                'image/jpeg',
            ],

            'jpeg' => [
                'image/jpeg',
            ],

            'png'  => [
                'image/png',
            ],
        ];
        
        $validated = [];
        $imagesOnly = true;
        foreach ($files as $file) {
            $extension = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
            $mime = is_uploaded_file($file['tmp_name']) ? (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) : '';
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !isset($allowed[$extension]) || !in_array($mime, $allowed[$extension], true)) {
                $errors[] = 'Only valid ZIP, PDF, DOCX, PPTX, JPG, JPEG, or PNG files are allowed.';
                continue;
            }
            if ((int)$file['size'] > 50 * 1024 * 1024) $errors[] = 'Files must be 50 MB or smaller.';
            $image = in_array($mime, ['image/jpeg', 'image/png'], true);
            if (!$image) $imagesOnly = false;
            $validated[] = $file + ['extension' => $extension, 'mime' => $mime];
        }
        if (count($files) > 1 && (!$imagesOnly || count($validated) !== count($files))) {
            $errors[] = 'Select one non-image kit file, or upload up to 10 images together.';
        }
        
        if ($errors) {
            $this->uploadError($async, $jobId, (int) $user['id'], implode(' ', $errors));
            return;
        }
        
        $root = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage';
        
        $folder = 'promotion-kits/images'; $directory = rtrim($root, '/\\').DIRECTORY_SEPARATOR.$folder;
        
        if (!is_dir($directory)) 
            mkdir($directory, 0700, true);
        
        $storedFiles = [];
        foreach ($validated as $file) {
            $stored = bin2hex(random_bytes(20)).'.'.$file['extension'];
            if (!move_uploaded_file($file['tmp_name'], $directory.DIRECTORY_SEPARATOR.$stored)) {
                foreach ($storedFiles as $storedFile) @unlink($directory.DIRECTORY_SEPARATOR.$storedFile['stored']);
                $this->uploadError($async, $jobId, (int) $user['id'], 'The file could not be stored.');
                return;
            }
            $storedFiles[] = ['stored' => $stored, 'path' => $folder.'/'.$stored];
        }
        $file = $validated[0];
        $primary = $storedFiles[0];

        $accessType = $_POST['access_type'] ?? 'request';

        if (!in_array($accessType, ['all', 'request'], true)) {
            $accessType = 'request';
        }
        
        try {
            db()->beginTransaction();
            $kitId = PromotionKit::create(['title'=>$title,'description'=>$description,'original'=>$file['name'],
                                  'stored'=>$primary['stored'],'path'=>$primary['path'],'extension'=>$file['extension'],'mime'=>$file['mime'],
                                  'size'=>(int)$file['size'],'cover'=>null,'access_type' => $accessType,
                                  'user_id'=>(int)$user['id']]);
            if ($imagesOnly) {
                $media = [];
                foreach ($validated as $index => $image) {
                    $media[] = ['path' => $storedFiles[$index]['path'], 'original' => $image['name'], 'mime' => $image['mime']];
                }
                PromotionKit::addMedia($kitId, $media);
            }
            db()->commit();
        } catch (Throwable $e) { 
            if (db()->inTransaction()) db()->rollBack();
            foreach ($storedFiles as $storedFile) @unlink($directory.DIRECTORY_SEPARATOR.$storedFile['stored']);
            $this->uploadError($async, $jobId, (int) $user['id'], 'The promotion kit could not be saved.');
            return;
        }

        if ($jobId > 0) {
            UploadJob::complete($jobId, (int) $user['id'], 'promotion_kit', $kitId);
        }

        if ($async) {
            $this->json(['ok' => true, 'message' => 'Promotion kit uploaded.', 'link' => '/promotion-kits/'.$kitId]);
            return;
        }
        
        flash('success', 'Promotion kit uploaded.'); 
        
        redirect('/promotion-kits');
    }

    private function uploadError(bool $async, int $jobId, int $userId, string $message): void
    {
        if ($jobId > 0) {
            UploadJob::fail($jobId, $userId, $message);
        }
        if ($async) {
            $this->json(['error' => $message], 422);
            return;
        }
        flash('error', $message);
        redirect('/promotion-kit-upload');
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
