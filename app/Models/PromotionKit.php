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

    public static function withMedia(array $kits): array
    {
        if (!$kits) return $kits;
        $ids = array_values(array_map(static fn(array $kit): int => (int)$kit['id'], $kits));
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $s = db()->prepare("SELECT promotion_kit_id, file_path, original_file_name, mime_type FROM promotion_kit_media WHERE promotion_kit_id IN ($marks) ORDER BY promotion_kit_id, sort_order, id");
        $s->execute($ids);
        $media = [];
        foreach ($s->fetchAll() as $item) $media[(int)$item['promotion_kit_id']][] = $item;
        foreach ($kits as &$kit) $kit['media'] = $media[(int)$kit['id']] ?? [];
        unset($kit);
        return $kits;
    }

    public static function addMedia(int $kitId, array $media): void
    {
        $s = db()->prepare('INSERT INTO promotion_kit_media (promotion_kit_id, file_path, original_file_name, mime_type, sort_order) VALUES (?, ?, ?, ?, ?)');
        foreach ($media as $index => $item) {
            $s->execute([$kitId, $item['path'], $item['original'], $item['mime'], $index]);
        }
    }

    public static function find(int $id): ?array
    {
        $s = db()->prepare('SELECT k.*, u.name AS uploader_name FROM promotion_kits k JOIN users u ON u.id=k.uploaded_by WHERE k.id=?');
        $s->execute([$id]);
        $kit = $s->fetch();
        if (!$kit) return null;
        return self::withMedia([$kit])[0];
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
