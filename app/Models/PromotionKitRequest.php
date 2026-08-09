<?php
declare(strict_types=1);

final class PromotionKitRequest
{
    public static function forUserAndKit(int $kitId, int $userId): ?array
    {
        $s = db()->prepare('SELECT * FROM promotion_kit_requests WHERE promotion_kit_id=? AND requested_by=?');
        $s->execute([$kitId, $userId]);
        return $s->fetch() ?: null;
    }

    public static function downloadable(int $kitId, int $userId): ?array
    {
        $s = db()->prepare("SELECT r.*, k.file_path, k.original_file_name, k.mime_type, k.file_size, k.status AS kit_status FROM promotion_kit_requests r JOIN promotion_kits k ON k.id=r.promotion_kit_id WHERE r.promotion_kit_id=? AND r.requested_by=? AND r.status='approved' AND k.status='active'");
        $s->execute([$kitId, $userId]);
        return $s->fetch() ?: null;
    }

    public static function allForReview(): array
    {
        return db()->query("SELECT r.*, k.title AS kit_title, k.status AS kit_status, u.name AS requester_name, u.email AS requester_email, v.name AS reviewer_name FROM promotion_kit_requests r JOIN promotion_kits k ON k.id=r.promotion_kit_id JOIN users u ON u.id=r.requested_by LEFT JOIN users v ON v.id=r.reviewed_by ORDER BY FIELD(r.status,'pending','approved','disapproved'), r.requested_at DESC")->fetchAll();
    }

    public static function create(int $kitId, int $userId): void
    {
        db()->prepare('INSERT INTO promotion_kit_requests (promotion_kit_id,requested_by) VALUES (?,?)')->execute([$kitId, $userId]);
    }

    public static function reopen(int $id): void
    {
        db()->prepare("UPDATE promotion_kit_requests SET status='pending', requested_at=NOW(), reviewed_at=NULL, reviewed_by=NULL, review_reason=NULL WHERE id=? AND status='disapproved'")->execute([$id]);
    }

    public static function review(int $id, string $status, int $reviewer, ?string $reason): void
    {
        db()->prepare(
            'UPDATE promotion_kit_requests SET status=?, reviewed_at=NOW(), reviewed_by=?, review_reason=? WHERE id=? AND status=?'
        )->execute([$status, $reviewer, $reason, $id, 'pending']);
    }
}
