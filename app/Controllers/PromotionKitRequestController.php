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

        /*
        * Resolve the file safely.
        */
        $root = realpath(
            config(
                'STORAGE_PATH',
                dirname(__DIR__, 2) . '/storage'
            )
        );

        $file = $root
            ? realpath(
                $root . DIRECTORY_SEPARATOR .
                ltrim($row['file_path'], '/\\')
            )
            : false;

        if (
            !$root ||
            !$file ||
            !str_starts_with(
                $file,
                $root . DIRECTORY_SEPARATOR
            ) ||
            !is_file($file) ||
            !is_readable($file)
        ) {
            http_response_code(404);

            render(
                'File unavailable',
                '<p>The promotion kit file could not be found.</p>'
            );

            exit;
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
        header(
            'Content-Type: ' .
            ($row['mime_type'] ?: 'application/octet-stream')
        );

        header(
            'Content-Length: ' . filesize($file)
        );

        header(
            'Content-Disposition: attachment; filename="' .
            str_replace(
                ['"', "\r", "\n"],
                '',
                basename($row['original_file_name'])
            ) .
            '"'
        );

        header('X-Content-Type-Options: nosniff');

        readfile($file);
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
        $user = require_super_admin(); csrf();
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $file = $_FILES['file'] ?? null;
        $errors = [];
        
        if ($title === '' || mb_strlen($title) > 150) $errors[] = 'Enter a title up to 150 characters.';

        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) $errors[] = 'Choose a file to upload.';
        
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
        
        $extension = strtolower(
            pathinfo((string)$file['name'], PATHINFO_EXTENSION)
        );

        $mime = '';

        if ($file && is_uploaded_file($file['tmp_name'])) {
            $mime = (new finfo(FILEINFO_MIME_TYPE))
                ->file($file['tmp_name']);
        }

        if (
            !isset($allowed[$extension]) ||
            !in_array($mime, $allowed[$extension], true)
        ) {
            $errors[] = 'Only valid ZIP, PDF, DOCX, PPTX, JPG, JPEG, or PNG files are allowed.';
        }
        
        if ($file && (int)$file['size'] > 50 * 1024 * 1024) 
            $errors[] = 'Files must be 50 MB or smaller.';
        
        if ($errors) { 
            flash('error', implode(' ', $errors)); 
            redirect('/promotion-kit-upload'); 
        }
        
        $root = config('STORAGE_PATH', dirname(__DIR__, 2).'/storage');
        
        $folder = 'promotion-kits/images'; $directory = rtrim($root, '/\\').DIRECTORY_SEPARATOR.$folder;
        
        if (!is_dir($directory)) 
            mkdir($directory, 0700, true);
        
        $stored = bin2hex(random_bytes(20)).'.'.$extension; $path = $folder.'/'.$stored;
        
        if (!move_uploaded_file($file['tmp_name'], $directory.DIRECTORY_SEPARATOR.$stored)) { 
            flash('error', 'The file could not be stored.'); 
            redirect('/promotion-kit-upload'); 
        }

        $accessType = $_POST['access_type'] ?? 'request';

        if (!in_array($accessType, ['all', 'request'], true)) {
            $accessType = 'request';
        }
        
        try { 
            PromotionKit::create(['title'=>$title,'description'=>$description,'original'=>$file['name'],
                                  'stored'=>$stored,'path'=>$path,'extension'=>$extension,'mime'=>$mime,
                                  'size'=>(int)$file['size'],'cover'=>null,'access_type' => $accessType,
                                  'user_id'=>(int)$user['id']]); 
        } catch (Throwable $e) { 
            @unlink($directory.DIRECTORY_SEPARATOR.$stored); 
            
            flash('error', 'The promotion kit could not be saved.'); 
            
            redirect('/promotion-kit-upload'); 
        }
        
        flash('success', 'Promotion kit uploaded.'); 
        
        redirect('/promotion-kits');
    }
}
