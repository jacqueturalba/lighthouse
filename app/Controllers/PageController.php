<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/View.php';
require_once __DIR__.'/../Models/PressRelease.php';

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
        require_auth();
        view('promotions/promotion-kits', ['title' => 'Promotion Kits']);
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
