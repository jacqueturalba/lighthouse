<?php
declare(strict_types=1);

final class UploadJob
{
    public static function create(int $userId, string $type, ?string $filename, ?int $size): int
    {
        $statement = db()->prepare(
            'INSERT INTO upload_jobs (user_id, upload_type, original_filename, file_size) VALUES (?, ?, ?, ?)'
        );
        $statement->execute([$userId, $type, $filename, $size]);
        return (int) db()->lastInsertId();
    }

    public static function updateProgress(int $id, int $userId, int $progress): bool
    {
        $statement = db()->prepare(
            "UPDATE upload_jobs SET status = 'uploading', progress = ? WHERE id = ? AND user_id = ? AND status IN ('preparing', 'uploading')"
        );
        $statement->execute([max(0, min(99, $progress)), $id, $userId]);
        return $statement->rowCount() > 0;
    }

    public static function complete(int $id, int $userId, string $type, int $relatedId): void
    {
        db()->prepare(
            "UPDATE upload_jobs SET status = 'completed', progress = 100, related_id = ?, error_message = NULL, completed_at = NOW() WHERE id = ? AND user_id = ? AND upload_type = ?"
        )->execute([$relatedId, $id, $userId, $type]);
    }

    public static function fail(int $id, int $userId, string $message): void
    {
        db()->prepare(
            "UPDATE upload_jobs SET status = 'failed', error_message = ? WHERE id = ? AND user_id = ? AND status <> 'completed'"
        )->execute([mb_substr($message, 0, 500), $id, $userId]);
    }

    public static function forUser(int $userId): array
    {
        // A request abandoned during navigation cannot notify PHP. Mark only
        // long-stale jobs failed; active browsers refresh progress regularly.
        db()->prepare(
            "UPDATE upload_jobs SET status = 'failed', error_message = 'The upload was interrupted. Please select the file and try again.' WHERE user_id = ? AND status IN ('preparing', 'uploading') AND updated_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)"
        )->execute([$userId]);

        $statement = db()->prepare(
            'SELECT id, upload_type, original_filename, file_size, status, progress, related_id, error_message, created_at, updated_at, completed_at FROM upload_jobs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10'
        );
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }
}
