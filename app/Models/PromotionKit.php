<?php
declare(strict_types=1);

final class PromotionKit
{
    public static function activeForUser(int $userId): array
    {
        $s = db()->prepare("SELECT k.*, u.name AS uploader_name, r.id AS request_id, r.status AS request_status, r.review_reason FROM promotion_kits k JOIN users u ON u.id=k.uploaded_by LEFT JOIN promotion_kit_requests r ON r.promotion_kit_id=k.id AND r.requested_by=? WHERE k.status='active' ORDER BY k.created_at DESC");
        $s->execute([$userId]);
        return $s->fetchAll();
    }

    public static function active(): array
    {
        return db()->query("SELECT k.*, u.name AS uploader_name FROM promotion_kits k JOIN users u ON u.id=k.uploaded_by WHERE k.status='active' ORDER BY k.created_at DESC")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $s = db()->prepare('SELECT k.*, u.name AS uploader_name FROM promotion_kits k JOIN users u ON u.id=k.uploaded_by WHERE k.id=?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $s = db()->prepare('INSERT INTO promotion_kits (title,description,original_file_name,stored_file_name,file_path,file_extension,mime_type,file_size,cover_photo_path,access_type,uploaded_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
        $s->execute([$data['title'], $data['description'], $data['original'], $data['stored'], $data['path'], $data['extension'], $data['mime'], $data['size'], $data['cover'], $data['access_type'], $data['user_id']]);
        return (int) db()->lastInsertId();
    }

    public static function archive(int $id): void
    {
        db()->prepare("UPDATE promotion_kits SET status='archived' WHERE id=?")->execute([$id]);
    }
}
