<?php
declare(strict_types=1);

require dirname(__DIR__).'/app/Controllers/ApplicationController.php';
require dirname(__DIR__).'/app/Controllers/StorageController.php';
require dirname(__DIR__).'/app/Controllers/PageController.php';
require dirname(__DIR__).'/app/Controllers/PromotionKitRequestController.php';
require dirname(__DIR__).'/app/Controllers/EventController.php';

$storage = new StorageController();
$router->get('/storage/{type}/{folder}/{file}', [$storage, 'show']);
$pages = new PageController();
$router->get('/', [$pages, 'home']);
$router->get('/press-releases', [$pages, 'pressReleases']);
$router->get('/promotion-kits', [$pages, 'promotionKits']);
$router->get('/promotion-kits/{id}', [$pages, 'promotionKitDetail']);
$router->get('/promotion-kit-requests', [$pages, 'promotionKitRequests']);
$router->get('/promotion-kit-upload', [$pages, 'promotionKitUpload']);
$requests = new PromotionKitRequestController();
$router->post('/promotion-kits/{id}/request', [$requests, 'store']);
$router->post('/promotion-kit-requests/{id}/approve', [$requests, 'approve']);
$router->post('/promotion-kit-requests/{id}/disapprove', [$requests, 'disapprove']);
$router->post('/promotion-kits/{id}/download', [$requests, 'download']);
$router->post('/promotion-kits/{id}/archive', [$requests, 'archive']);
$router->post('/promotion-kit-upload', [$requests, 'upload']);
$router->get('/calendar', [$pages, 'calendar']);
$router->get('/events/{id}', [$pages, 'eventDetail']);
$router->get('/event-review', [$pages, 'eventReview']);
$events = new EventController();
$router->post('/events', [$events, 'store']);
$router->post('/events/{id}/review', [$events, 'review']);
$router->get('/trainings', [$pages, 'placeholder']);
$router->get('/guidelines', [$pages, 'placeholder']);
$router->get('/static-1', [$pages, 'placeholder']);
$router->get('/static-2', [$pages, 'placeholder']);
$application = new ApplicationController();
$router->any('/', [$application, 'dispatch']);
$router->any('/{path}', [$application, 'dispatch']);
