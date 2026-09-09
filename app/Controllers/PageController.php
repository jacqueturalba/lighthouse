<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/View.php';
require_once __DIR__.'/../Models/PressRelease.php';
require_once __DIR__.'/../Models/PromotionKit.php';
require_once __DIR__.'/../Models/PromotionKitRequest.php';
require_once __DIR__.'/../Models/PEvent.php';
require_once __DIR__.'/../Models/Organizer.php';
require_once __DIR__.'/../Models/MaterialRequest.php';
require_once __DIR__.'/../Models/SFlexPost.php';

final class PageController
{
    public function home(): void { 
        $user = require_auth(); 
        $latestRelease = PressRelease::getPressReleaseById(0);
        $thisWeekEvents = PEvent::thisWeek(8);

        view('dashboard/home', ['title' => 'Homepage',
         'user' => $user,
         'latestRelease' => $latestRelease,
         'thisWeekEvents' => $thisWeekEvents
         ]); 
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

        view('pressreleases/press-releases', [
            'title' => 'Press Releases',
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

        view('pressreleases/press-release-edit', [
            'title' => 'Press Releases',
            'pressRelease' => $pressRelease,
        ]);
    }

    public function pressReleaseUpload(): void {
        require_super_admin();
        view('pressreleases/press-release-upload', ['title' => 'Add Press Release']);
    }

    public function calendar(): void
    {
        $user = require_auth();

        $month = trim((string)($_GET['month'] ?? date('Y-m')));

        $date = DateTimeImmutable::createFromFormat('!Y-m', $month)
            ?: new DateTimeImmutable('first day of this month');

        $month = $date->format('Y-m');

        $view = isset($_GET['view'])
            && in_array($_GET['view'], ['month', 'week', 'day'], true)
            ? $_GET['view']
            : 'month';

        $selected = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            (string)($_GET['selected'] ?? date('Y-m-d'))
        ) ?: new DateTimeImmutable('today');


        if ($view === 'week') {

            $from = $selected->modify(
                '-' . ((int)$selected->format('N') - 1) . ' days'
            );

            $to = $from->modify('+6 days');

        } elseif ($view === 'day') {

            $from = $to = $selected;

        } else {

            $from = $date
                ->modify('first day of this month')
                ->modify('-' . ((int)$date->format('N') - 1) . ' days');

            $lastDay = $date->modify('last day of this month');

            $to = $lastDay->modify(
                '+' . (7 - (int)$lastDay->format('N')) . ' days'
            );
        }


        $oid = (int)($_GET['organizer'] ?? 0);

        $oid = $oid > 0 && Organizer::exists($oid)
            ? $oid
            : null;


        /*
        |--------------------------------------------------------------------------
        | Pagination settings
        |--------------------------------------------------------------------------
        */

        $per = (string)($_GET['per_page'] ?? '5');

        $limit = in_array(
            $per,
            ['5', '10', '50', '100', 'all'],
            true
        )
            ? ($per === 'all' ? null : (int)$per)
            : 5;


        $pper = (string)($_GET['pending_per_page'] ?? '5');

        $plimit = in_array(
            $pper,
            ['5', '10', '50', '100', 'all'],
            true
        )
            ? ($pper === 'all' ? null : (int)$pper)
            : 5;


        /*
        |--------------------------------------------------------------------------
        | AJAX pagination
        |--------------------------------------------------------------------------
        */

        if (
            isset($_GET['ajax'])
            && $_GET['ajax'] === '1'
        ) {

            $type = $_GET['type'] ?? '';

            header('Content-Type: application/json; charset=utf-8');


            if ($type === 'upcoming') {

                $page = max(1, (int)($_GET['page'] ?? 1));

                echo json_encode(
                    PEvent::paginated(
                        'approved',
                        $page,
                        $limit,
                        $oid
                    )
                );

                exit;
            }


            if ($type === 'pending') {

                $page = max(1, (int)($_GET['page'] ?? 1));

                echo json_encode(
                    PEvent::paginated(
                        'pending',
                        $page,
                        $plimit,
                        $oid,
                        (int)$user['id']
                    )
                );

                exit;
            }


            http_response_code(400);

            echo json_encode([
                'error' => 'Invalid pagination type.'
            ]);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Normal page render
        |--------------------------------------------------------------------------
        */

        $upcomingPage = max(
            1,
            (int)($_GET['page'] ?? 1)
        );

        $pendingPage = max(
            1,
            (int)($_GET['pending_page'] ?? 1)
        );


        view('calendar/index', [
            'title' => 'Calendar',

            'month' => $month,
            'monthDate' => $date,

            'gridStart' => $from,
            'gridEnd' => $to,

            'view' => $view,
            'selected' => $selected,

            'organizerId' => $oid,
            'organizers' => Organizer::all(),

            'events' => PEvent::calendar(
                $from->format('Y-m-d'),
                $to->format('Y-m-d'),
                $oid
            ),

            'upcoming' => PEvent::paginated(
                'approved',
                $upcomingPage,
                $limit,
                $oid
            ),

            'pending' => PEvent::paginated(
                'pending',
                $pendingPage,
                $plimit,
                $oid,
                (int)$user['id']
            ),

            'per' => $per,
            'pper' => $pper
        ]);
    }

    public function eventDetail(array $params): void {
        
        $user = require_auth();
        $admin = $user;

        $event = PEvent::find((int)$params['id']);
        /*if (!$event || ($event['status'] !== 'approved' 
            && (int)$event['submitted_by'] !== (int)current_user()['id'] 
            && current_user()['role'] !== 'super_admin')) { 
                http_response_code(404); 
                render('Event not found', '<p>This event is not available.</p>'); 
                return; 
        }*/

        if (!$event || ($event['status'] !== 'approved' 
            && (int)$event['submitted_by'] !== (int)current_user()['id'])) { 
                http_response_code(404); 
                render('Event not found', '<p>This event is not available.</p>'); 
                return; 
        }        
        view('calendar/detail', ['title'=>$event['title'], 'admin'=>$admin, 'event'=>$event, 
                                 'materialRequest'=>MaterialRequest::findByEvent((int)$event['id']), 'user'=>current_user()]);
    }

    public function eventReview(): void {
        require_super_admin();
        view('calendar/review', ['title'=>'Event Review', 'events'=>PEvent::forReview()]);
    }

    public function eventEdit(array $params): void {
        $user=require_auth();
        $event=PEvent::find((int)$params['id']);
        if(!$event||!($user['role']==='super_admin'||((int)$event['submitted_by']===(int)$user['id']&&$event['status']==='pending'))){
            http_response_code(404);
            render('Event not found','<p>This event is not available.</p>');
            return;
        }
        view('calendar/edit',['title'=>'Edit event','event'=>$event,'organizers'=>Organizer::all()]);
    }

    public function organizers(): void {
        require_super_admin();
        view('calendar/organizers',['title'=>'Manage organizers','organizers'=>Organizer::custom()]);
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
    public function sflex(): void {$u=require_auth();view('sflex/index',['title'=>'SFlex','posts'=>SFlexPost::feed((int)$u['id'],0)]);}
    public function sflexCreate(): void {require_auth();view('sflex/create',['title'=>'Create post']);}
    public function sflexReview(): void {require_super_admin();view('sflex/review',['title'=>'Review SFlex posts','posts'=>SFlexPost::pending()]);}
}
