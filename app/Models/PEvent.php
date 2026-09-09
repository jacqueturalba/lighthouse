<?php
declare(strict_types=1);
final class PEvent
{
    private static function select(): string
    {
        return "SELECT e.*,u.name AS submitter_name,r.name AS reviewer_name,COALESCE(o.name,e.organizer) AS organizer,COALESCE(o.color,'#145da0') AS organizer_color FROM events e JOIN users u ON u.id=e.submitted_by LEFT JOIN users r ON r.id=e.reviewed_by LEFT JOIN event_organizers o ON o.id=e.organizer_id";
    }
    public static function find(int $id): ?array
    {
        $s = db()->prepare(self::select() . " WHERE e.id=? LIMIT 1");
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
    public static function calendar(string $from, string $to, ?int $oid): array
    {
        $q =
            self::select() .
            " WHERE e.status='approved' AND e.event_date BETWEEN ? AND ?";
        $a = [$from, $to];
        if ($oid) {
            $q .= " AND e.organizer_id=?";
            $a[] = $oid;
        }
        $q .= " ORDER BY e.event_date,e.start_time,e.title";
        $s = db()->prepare($q);
        $s->execute($a);
        return $s->fetchAll();
    }
    public static function paginated(
        string $status,
        int $page,
        ?int $per=100,
        ?int $oid,
        ?int $uid = null
    ): array {
        $w = ["e.status=?"];
        $a = [$status];
        if ($status === "approved") {
            $w[] = "e.event_date>=CURDATE()";
        }
        if ($oid) {
            $w[] = "e.organizer_id=?";
            $a[] = $oid;
        }
        if ($status === "pending" && $uid) {
            $w[] = "(e.submitted_by=? OR e.event_date>=CURDATE())";
            $a[] = $uid;
        }
        $where = " WHERE " . implode(" AND ", $w);
        $s = db()->prepare("SELECT COUNT(*) FROM events e" . $where);
        $s->execute($a);
        $total = (int) $s->fetchColumn();
        $pages = $per ? max(1, (int) ceil($total / $per)) : 1;
        $page = min(max(1, $page), $pages);
        $q =
            self::select() .
            $where .
            " ORDER BY e.event_date,e.created_at DESC";
        if ($per) {
            $q .=
                " LIMIT " .
                (int) $per .
                " OFFSET " .
                (int) (($page - 1) * $per);
        }
        $s = db()->prepare($q);
        $s->execute($a);
        return [
            "items" => $s->fetchAll(),
            "total" => $total,
            "page" => $page,
            "pages" => $pages,
        ];
    }
    public static function forReview(): array
    {
        return db()
            ->query(
                self::select() .
                    " WHERE e.status='pending' ORDER BY e.created_at ASC"
            )
            ->fetchAll();
    }
    public static function mine(int $uid): array
    {
        $s = db()->prepare(
            self::select() .
                " WHERE e.submitted_by=? ORDER BY e.event_date DESC,e.created_at DESC"
        );
        $s->execute([$uid]);
        return $s->fetchAll();
    }
    public static function create(array $d, int $uid): int
    {
        $s = db()->prepare(
            "INSERT INTO events (title,description,event_date,start_time,end_time,location,organizer,organizer_id,website_url,material_request,submitted_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)"
        );
        $s->execute([
            $d["title"],
            $d["description"],
            $d["event_date"],
            $d["start_time"] ?: null,
            $d["end_time"] ?: null,
            $d["location"],
            $d["organizer"],
            $d["organizer_id"],
            $d["website_url"] ?: null,
            $d["material_request"] ?: null,
            $uid,
        ]);
        return (int) db()->lastInsertId();
    }
    public static function update(int $id, array $d): void
    {
        $s = db()->prepare(
            "UPDATE events SET title=?,description=?,event_date=?,start_time=?,end_time=?,location=?,organizer=?,organizer_id=?,website_url=?,material_request=? WHERE id=?"
        );
        $s->execute([
            $d["title"],
            $d["description"],
            $d["event_date"],
            $d["start_time"] ?: null,
            $d["end_time"] ?: null,
            $d["location"],
            $d["organizer"],
            $d["organizer_id"],
            $d["website_url"] ?: null,
            $d["material_request"] ?: null,
            $id,
        ]);
    }
    public static function delete(int $id): bool
    {
        $s = db()->prepare("DELETE FROM events WHERE id=?");
        $s->execute([$id]);
        return $s->rowCount() > 0;
    }
    public static function review(
        int $id,
        string $status,
        int $rid,
        ?string $reason
    ): void {
        $s = db()->prepare(
            'UPDATE events SET status=?,reviewed_by=?,review_reason=?,reviewed_at=NOW() WHERE id=? AND status="pending"'
        );
        $s->execute([$status, $rid, $reason, $id]);
    }
    public static function thisWeek(int $limit = 8): array
    {
        $s = db()->prepare(
            self::select() .
                " WHERE e.status='approved' AND e.event_date BETWEEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) AND DATE_ADD(CURDATE(),INTERVAL 7 DAY) ORDER BY e.event_date,e.start_time,e.title LIMIT ?"
        );
        $s->bindValue(1, max(1, min(24, $limit)), PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }
}
