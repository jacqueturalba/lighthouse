<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Auth.php';
require_once dirname(__DIR__) . '/Models/UploadJob.php';

final class UploadController
{
    public function start(): void
    {
        $user = require_auth();
        csrf();
        $type = (string) ($_POST['upload_type'] ?? '');
        if (!in_array($type, ['promotion_kit', 'sflex'], true)) {
            $this->json(['error' => 'Invalid upload type.'], 422);
            return;
        }
        if ($type === 'promotion_kit' && $user['role'] !== 'super_admin') {
            $this->json(['error' => 'You do not have permission to upload promotion kits.'], 403);
            return;
        }

        $id = UploadJob::create(
            (int) $user['id'],
            $type,
            trim((string) ($_POST['original_filename'] ?? '')) ?: null,
            max(0, (int) ($_POST['file_size'] ?? 0)) ?: null
        );
        $this->json(['id' => $id, 'status' => 'preparing']);
    }

    public function progress(array $params): void
    {
        $user = require_auth();
        csrf();
        $updated = UploadJob::updateProgress((int) $params['id'], (int) $user['id'], (int) ($_POST['progress'] ?? 0));
        $this->json(['ok' => $updated], $updated ? 200 : 404);
    }

    public function fail(array $params): void
    {
        $user = require_auth();
        csrf();
        UploadJob::fail((int) $params['id'], (int) $user['id'], trim((string) ($_POST['message'] ?? 'The upload failed.')));
        $this->json(['ok' => true]);
    }

    public function status(): void
    {
        $user = require_auth();
        $this->json(['jobs' => UploadJob::forUser((int) $user['id'])]);
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
