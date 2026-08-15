<?php
declare(strict_types=1);

require dirname(__DIR__).'/app/Controllers/ApplicationController.php';
require dirname(__DIR__).'/app/Controllers/StorageController.php';
require dirname(__DIR__).'/app/Controllers/PageController.php';
require dirname(__DIR__).'/app/Controllers/PromotionKitRequestController.php';
require dirname(__DIR__).'/app/Controllers/EventController.php';
require dirname(__DIR__).'/app/Controllers/MaterialRequestController.php';
require dirname(__DIR__).'/app/Controllers/PressReleaseController.php';

$storage = new StorageController();
$router->get('/storage/{type}/{folder}/{file}', [$storage, 'show']);

$pages = new PageController();
$router->get('/', [$pages, 'home']);

$router->get('/calendar', [$pages, 'calendar']);
$router->get('/events/{id}', [$pages, 'eventDetail']);
$router->get('/event-review', [$pages, 'eventReview']);

$router->get('/press-releases', [$pages, 'pressReleases']);
$router->get('/press-release-upload', [$pages, 'pressReleaseUpload']);
$router->get('/press-release-edit', [$pages, 'pressReleaseUpdate']);

$router->get('/promotion-kits', [$pages, 'promotionKits']);
$router->get('/promotion-kits/{id}', [$pages, 'promotionKitDetail']);
$router->get('/promotion-kit-requests', [$pages, 'promotionKitRequests']);
$router->get('/promotion-kit-upload', [$pages, 'promotionKitUpload']);

$router->get('/trainings', [$pages, 'placeholder']);
$router->get('/static-1', [$pages, 'placeholder']);
$router->get('/static-2', [$pages, 'placeholder']);

$releases = new PressReleaseController();
$router->post('/press-release-upload', [$releases, 'store']);
$router->post('/press-release-edit', [$releases, 'update']);
$router->post('/press-release-delete', [$releases, 'delete']);

$requests = new PromotionKitRequestController();
$router->post('/promotion-kits/{id}/request', [$requests, 'store']);
$router->post('/promotion-kit-requests/{id}/approve', [$requests, 'approve']);
$router->post('/promotion-kit-requests/{id}/disapprove', [$requests, 'disapprove']);
$router->post('/promotion-kits/{id}/download', [$requests, 'download']);
$router->post('/promotion-kits/{id}/archive', [$requests, 'archive']);
$router->post('/promotion-kit-upload', [$requests, 'upload']);

$events = new EventController();
$router->post('/events', [$events, 'store']);
$router->post('/events/{id}/review', [$events, 'review']);

$materialRequests = new MaterialRequestController();
$router->get('/material-requests', [$pages, 'materialRequests']);
$router->get('/material-requests/{id}', [$pages, 'materialRequestDetail']);
$router->post('/events/{id}/material-request', [$materialRequests, 'store']);
$router->post('/material-requests/{id}', [$materialRequests, 'update']);


$application = new ApplicationController();
$router->any('/', [$application, 'dispatch']);
$router->any('/{path}', [$application, 'dispatch']);
