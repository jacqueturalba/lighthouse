<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/View.php';
require_once __DIR__.'/../Models/PressRelease.php';
require_once __DIR__.'/../Models/PromotionKit.php';
require_once __DIR__.'/../Models/PromotionKitRequest.php';

final class PageController
{
    public function home(): void { 
        require_auth(); 
        $latestRelease = PressRelease::getPressReleaseById(0);

        view('dashboard/home', ['title' => 'Homepage',
         'latestRelease' => $latestRelease]
         ); 
    }

    public function promotionKits(): void {
        $user = require_auth();
        view('promotions/promotion-kits', ['title' => 'Promotion Kits', 'kits' => PromotionKit::activeForUser((int)$user['id'])]);
    }

    public function promotionKitDetail(array $params): void {
        $user = require_auth();
        $kit = PromotionKit::find((int)$params['id']);
        if (!$kit || $kit['status'] !== 'active') { http_response_code(404); render('Promotion kit not found', '<p>This promotion kit is no longer available.</p>'); return; }
        view('promotions/promotion-kit-detail', ['title' => $kit['title'], 'kit' => $kit, 'request' => PromotionKitRequest::forUserAndKit((int)$kit['id'], (int)$user['id'])]);
    }

    public function promotionKitRequests(): void {
        require_super_admin();
        view('promotions/promotion-kit-requests', ['title' => 'Promotion Kit Requests', 'requests' => PromotionKitRequest::allForReview()]);
    }

    public function promotionKitUpload(): void {
        require_super_admin();
        view('promotions/promotion-kit-upload', ['title' => 'Upload Promotion Kit']);
    }

    public function pressReleases(): void {
        require_auth();

        $currentPage = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $pagination = PressRelease::getPaginatedPressReleases(
            $currentPage,
            4
        );

        view('releases/press-releases', [
            'title' => 'Press Releases',
            'latestRelease' => $pagination['latest'],
            'pressReleases' => $pagination['items'],
            'currentPage' => $pagination['currentPage'],
            'totalPages' => $pagination['totalPages'],
            'totalItems' => $pagination['totalItems'],
        ]);
    }

    public function placeholder(array $params = []): void { 
        require_auth(); 
        $page = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/'); 
        view('static/placeholder', ['title' => ucwords(str_replace('-', ' ', $page))]); 
    }
}
