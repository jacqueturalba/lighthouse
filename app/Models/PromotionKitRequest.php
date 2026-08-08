<?php
declare(strict_types=1);
final class PromotionKitRequest
{
    public static function forUserAndKit(int $kitId,int $userId): ?array { $s=db()->prepare('SELECT * FROM promotion_kit_requests WHERE promotion_kit_id=? AND requested_by=?');$s->execute([$kitId,$userId]);return $s->fetch()?:null; }
    public static function create(int $kitId,int $userId): void { db()->prepare('INSERT INTO promotion_kit_requests (promotion_kit_id,requested_by) VALUES (?,?)')->execute([$kitId,$userId]); }
    public static function review(int $id,string $status,int $reviewer,?string $reason): void { db()->prepare('UPDATE promotion_kit_requests SET status=?,reviewed_at=NOW(),reviewed_by=?,review_reason=? WHERE id=?')->execute([$status,$reviewer,$reason,$id]); }
}
