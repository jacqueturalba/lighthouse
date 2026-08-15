<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/View.php';
require_once __DIR__.'/../Models/PressRelease.php';
require_once __DIR__.'/../Models/PromotionKit.php';
require_once __DIR__.'/../Models/PromotionKitRequest.php';
require_once __DIR__.'/../Models/PEvent.php';
require_once __DIR__.'/../Models/MaterialRequest.php';

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
        if (!$kit || $kit['status'] !== 'active') { 
            http_response_code(404); 
            render('Promotion kit not found', '<p>This promotion kit is no longer available.</p>'); 
            return; 
        }
        view('promotions/promotion-kit-detail', ['title' => $kit['title'], 'kit' => $kit, 
                                                 'request' => PromotionKitRequest::forUserAndKit((int)$kit['id'], (int)$user['id'])]);
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

        $prid = (int) ($_GET['p'] ?? 0);

        $pagination = PressRelease::getPaginatedPressReleases(
            $currentPage,
            4,
            $prid
        );

        view('releases/press-releases', [
            'title' => 'Press Releases',
            'allReleases' => PressRelease::getPressReleases(4, 0),
            'latestRelease' => $pagination['latest'],
            'latestLinks' => $pagination['latestLinks'],
            'pressReleases' => $pagination['items'],
            'currentPage' => $pagination['currentPage'],
            'totalPages' => $pagination['totalPages'],
            'totalItems' => $pagination['totalItems'],
        ]);
    }

    public function pressReleaseUpdate(): void {
        require_auth();
        
        $id = (int) ($_GET['id'] ?? 0);

        if (empty($id)) {
            flash('error','No press release is selected.'); 
            redirect('/press-releases');
        }

        $pressRelease = PressRelease::getPressReleaseById($id);

        if (!$pressRelease) {
            flash('error', 'Press release not found.');
            redirect('/press-releases');
        }

        view('releases/press-release-edit', [
            'title' => 'Press Releases',
            'pressRelease' => $pressRelease,
        ]);
    }

    public function pressReleaseUpload(): void {
        require_super_admin();
        view('releases/press-release-upload', ['title' => 'Add Press Release']);
    }

    public function calendar(): void {
        require_auth();
        $month = trim((string)($_GET['month'] ?? date('Y-m')));
        $date = DateTimeImmutable::createFromFormat('!Y-m', $month) ?: new DateTimeImmutable('first day of this month');
        $month = $date->format('Y-m');
        $from = $date->modify('first day of this month')->modify('-'.((int)$date->format('N') - 1).' days');
        $to = $date->modify('last day of this month')->modify('+'.(7 - (int)$date->modify('last day of this month')->format('N')).' days');
        view('calendar/index', ['title'=>'Calendar', 'month'=>$month, 'monthDate'=>$date, 'gridStart'=>$from, 
                                'gridEnd'=>$to, 'events'=>PEvent::month($from->format('Y-m-d'), $to->format('Y-m-d')), 
                                'upcoming'=>PEvent::upcoming(), 'mine'=>PEvent::mine((int)current_user()['id']), 
                                'pending'=>PEvent::pendingVisible((int)current_user()['id'])]);
    }

    public function eventDetail(array $params): void {
        require_auth();
        $event = PEvent::find((int)$params['id']);
        if (!$event || ($event['status'] !== 'approved' 
            && (int)$event['submitted_by'] !== (int)current_user()['id'] 
            && current_user()['role'] !== 'super_admin')) { 
                http_response_code(404); 
                render('Event not found', '<p>This event is not available.</p>'); 
                return; 
        }
        view('calendar/detail', ['title'=>$event['title'], 'event'=>$event, 
                                 'materialRequest'=>MaterialRequest::findByEvent((int)$event['id']), 'user'=>current_user()]);
    }

    public function eventReview(): void {
        require_super_admin();
        view('calendar/review', ['title'=>'Event Review', 'events'=>PEvent::forReview()]);
    }

    public function materialRequests(): void {
        $user = require_auth();
        $admin = $user['role'] === 'super_admin';
        view('materials/index', ['title'=>'Material Requests', 'admin'=>$admin, 
                                 'requests'=>$admin ? MaterialRequest::forAdmin() : MaterialRequest::forRequester((int)$user['id'])]);
    }

    public function materialRequestDetail(array $params): void {
        $user = require_auth();
        $request = MaterialRequest::find((int)$params['id']);
        if (!$request || ($user['role'] !== 'super_admin' 
            && (int)$request['requested_by'] !== (int)$user['id'])) { 
                http_response_code(404); 
                render('Material request not found', '<p>This material request is not available.</p>'); 
                return; 
        }
        view('materials/detail', ['title'=>'Material Request', 'request'=>$request, 'admin'=>$user['role'] === 'super_admin', 
                                  'kits'=>$user['role'] === 'super_admin' ? PromotionKit::active() : []]);
    }

    public function placeholder(array $params = []): void { 
        require_auth(); 
        $page = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/'); 
        view('static/placeholder', ['title' => ucwords(str_replace('-', ' ', $page))]); 
    }
}
