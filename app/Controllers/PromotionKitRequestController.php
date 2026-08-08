<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/Auth.php';
require_once dirname(__DIR__).'/Services/PromotionKitRequestService.php';
final class PromotionKitRequestController
{
    private PromotionKitRequestService $service;
    public function __construct(){ $this->service=new PromotionKitRequestService(); }
    public function store(array $params): void { $user=require_auth(); csrf(); flash('success',$this->service->request((int)$params['id'],(int)$user['id'])); redirect('/promotion-kits'); }
    public function approve(array $params): void { $user=require_super_admin(); csrf(); flash('success',$this->service->review((int)$params['id'],'approved',$user,null)); redirect('/promotion-kit-requests'); }
    public function disapprove(array $params): void { $user=require_super_admin(); csrf(); $message=$this->service->review((int)$params['id'],'disapproved',$user,$_POST['reason']??''); flash(str_contains($message,'required')?'error':'success',$message); redirect('/promotion-kit-requests'); }
}
