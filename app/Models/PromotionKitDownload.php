<?php
declare(strict_types=1);
final class PromotionKitDownload {
    public static function record(int $kitId,int $userId,int $requestId): void { 
        db()->prepare('INSERT INTO promotion_kit_downloads (promotion_kit_id,user_id,request_id) VALUES (?,?,?)')->execute([$kitId,$userId,$requestId]); 
    }
}
