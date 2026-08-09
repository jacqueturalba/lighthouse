<?php
declare(strict_types=1);
require_once __DIR__.'/../Models/PromotionKit.php';
require_once __DIR__.'/../Models/PromotionKitRequest.php';

final class PromotionKitRequestService
{
    public function request(int $kitId, int $userId): string
    {
        $kit=PromotionKit::find($kitId); if(!$kit || $kit['status']!=='active') return 'This promotion kit is unavailable.';
        $existing = PromotionKitRequest::forUserAndKit($kitId,$userId);
        if ($existing && $existing['status'] !== 'disapproved') return 'You have already requested this promotion kit.';
        try { if ($existing) PromotionKitRequest::reopen((int)$existing['id']); else PromotionKitRequest::create($kitId,$userId); log_event('promotion_kit_requested',['kit_id'=>$kitId,'user_id'=>$userId]); return 'Download request submitted.'; }
        catch(PDOException $e) { return 'You have already requested this promotion kit.'; }
    }
    public function review(int $requestId,string $status,array $reviewer,?string $reason): string
    {
        if($reviewer['role']!=='super_admin') throw new RuntimeException('Forbidden');
        if($status==='disapproved' && trim((string)$reason)==='') return 'A disapproval reason is required.';
        if (!in_array($status, ['approved', 'disapproved'], true)) return 'Invalid review status.';
        PromotionKitRequest::review($requestId,$status,(int)$reviewer['id'],$status==='disapproved'?trim((string)$reason):null);
        log_event('promotion_kit_request_'.$status,['request_id'=>$requestId,'reviewer_id'=>$reviewer['id']]); return 'Request '.$status.'.';
    }
}
