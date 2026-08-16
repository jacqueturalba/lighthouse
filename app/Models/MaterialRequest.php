<?php
declare(strict_types=1);

final class MaterialRequest
{
    public static function findByEvent(int $eventId): ?array
    {
        $stmt = db()->prepare("SELECT mr.*, e.title AS event_title, e.event_date, e.location, e.organizer, e.submitted_by, u.name AS requester_name, k.title AS kit_title FROM material_requests mr JOIN events e ON e.id=mr.event_id JOIN users u ON u.id=mr.requested_by LEFT JOIN promotion_kits k ON k.id=mr.promotion_kit_id WHERE mr.event_id=? LIMIT 1");
        $stmt->execute([$eventId]);
        return $stmt->fetch() ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT mr.*, e.title AS event_title, e.event_date, e.location, e.organizer, e.submitted_by, u.name AS requester_name, k.title AS kit_title FROM material_requests mr JOIN events e ON e.id=mr.event_id JOIN users u ON u.id=mr.requested_by LEFT JOIN promotion_kits k ON k.id=mr.promotion_kit_id WHERE mr.id=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function forRequester(int $userId): array
    {
        $stmt = db()->prepare("SELECT mr.*, e.title AS event_title, e.event_date, e.location, k.title AS kit_title FROM material_requests mr JOIN events e ON e.id=mr.event_id LEFT JOIN promotion_kits k ON k.id=mr.promotion_kit_id WHERE mr.requested_by=? ORDER BY mr.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function forAdmin(): array
    {
        return db()->query("SELECT mr.*, e.title AS event_title, e.event_date, e.location, u.name AS requester_name, k.title AS kit_title FROM material_requests mr JOIN events e ON e.id=mr.event_id JOIN users u ON u.id=mr.requested_by LEFT JOIN promotion_kits k ON k.id=mr.promotion_kit_id WHERE mr.status NOT IN ('delivered','cancelled') ORDER BY mr.deadline IS NULL, mr.deadline, mr.created_at")->fetchAll();
    }

    public static function create(array $data, int $eventId, int $userId): int
    {
        $stmt = db()->prepare('INSERT INTO material_requests (event_id,requested_by,material_types,video_specs,image_specs,event_content,additional_instructions) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$eventId, $userId, json_encode($data['material_types'], JSON_THROW_ON_ERROR), $data['video_specs'] ? json_encode($data['video_specs'], JSON_THROW_ON_ERROR) : null, $data['image_specs'] ? json_encode($data['image_specs'], JSON_THROW_ON_ERROR) : null, $data['event_content'], $data['additional_instructions'] ?: null]);
        db()->prepare('UPDATE events SET material_request=? WHERE id=?')->execute([$data['event_content'], $eventId]);
        return (int)db()->lastInsertId();
    }

    public static function updateAdmin(int $id, string $status, ?string $deadline, ?int $kitId, ?string $notes): void
    {
        $stmt = db()->prepare('UPDATE material_requests SET status=?, deadline=?, promotion_kit_id=?, admin_notes=? WHERE id=?');
        $stmt->execute([$status, $deadline ?: null, $kitId ?: null, $notes ?: null, $id]);
    }
}
