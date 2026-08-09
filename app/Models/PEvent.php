<?php
declare(strict_types=1);

final class PEvent
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT e.*, u.name AS submitter_name, r.name AS reviewer_name FROM events e JOIN users u ON u.id=e.submitted_by LEFT JOIN users r ON r.id=e.reviewed_by WHERE e.id=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function month(string $from, string $to): array
    {
        $stmt = db()->prepare("SELECT e.*, u.name AS submitter_name FROM events e JOIN users u ON u.id=e.submitted_by WHERE e.status='approved' AND e.event_date BETWEEN ? AND ? ORDER BY e.event_date, e.start_time, e.title");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }

    public static function upcoming(int $limit = 8): array
    {
        $limit = max(1, min(24, $limit));
        return db()->query("SELECT e.*, u.name AS submitter_name FROM events e JOIN users u ON u.id=e.submitted_by WHERE e.status='approved' AND e.event_date >= CURDATE() ORDER BY e.event_date, e.start_time, e.title LIMIT {$limit}")->fetchAll();
    }

    public static function forReview(): array
    {
        return db()->query("SELECT e.*, u.name AS submitter_name FROM events e JOIN users u ON u.id=e.submitted_by WHERE e.status='pending' ORDER BY e.created_at ASC")->fetchAll();
    }

    public static function mine(int $userId): array
    {
        $stmt = db()->prepare('SELECT * FROM events WHERE submitted_by=? ORDER BY event_date DESC, created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data, int $userId): int
    {
        $stmt = db()->prepare('INSERT INTO events (title,description,event_date,start_time,end_time,location,organizer,website_url,material_request,submitted_by) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$data['title'], $data['description'], $data['event_date'], $data['start_time'] ?: null, $data['end_time'] ?: null, $data['location'], $data['organizer'], $data['website_url'] ?: null, $data['material_request'] ?: null, $userId]);
        return (int)db()->lastInsertId();
    }

    public static function review(int $id, string $status, int $reviewerId, ?string $reason): void
    {
        $stmt = db()->prepare('UPDATE events SET status=?, reviewed_by=?, review_reason=?, reviewed_at=NOW() WHERE id=? AND status="pending"');
        $stmt->execute([$status, $reviewerId, $reason, $id]);
    }
}
