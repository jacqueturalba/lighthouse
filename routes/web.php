<?php
declare(strict_types=1);

require dirname(__DIR__).'/app/Controllers/ApplicationController.php';
require dirname(__DIR__).'/app/Controllers/StorageController.php';
require dirname(__DIR__).'/app/Controllers/PageController.php';
require dirname(__DIR__).'/app/Controllers/PromotionKitRequestController.php';
require dirname(__DIR__).'/app/Controllers/EventController.php';
require dirname(__DIR__).'/app/Controllers/MaterialRequestController.php';
require dirname(__DIR__).'/app/Controllers/PressReleaseController.php';
require dirname(__DIR__).'/app/Controllers/SFlexController.php';
require dirname(__DIR__).'/app/Controllers/UploadController.php';

$storage = new StorageController();
$router->get('/storage/{type}/{folder}/{file}', [$storage, 'show']);

$pages = new PageController();
$router->get('/', [$pages, 'home']);

$router->get('/calendar', [$pages, 'calendar']);
$router->get('/sflex', [$pages, 'sflex']);
$router->get('/sflex/create', [$pages, 'sflexCreate']);
$router->get('/sflex/review', [$pages, 'sflexReview']);
$router->get('/sflex/rejected', [$pages, 'sflexRejected']);
$router->get('/sflex/submissions', [$pages, 'sflexSubmissions']);
$router->get('/sflex/post/{id}', [$pages, 'sflexPost']);
$router->get('/events/{id}', [$pages, 'eventDetail']);
$router->get('/events/{id}/edit', [$pages, 'eventEdit']);
$router->get('/organizers', [$pages, 'organizers']);
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
$router->post('/press-release-new', [$releases, 'store']);
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
$sflex = new SFlexController();
$uploads = new UploadController();
$router->post('/uploads/start', [$uploads, 'start']);
$router->post('/uploads/{id}/progress', [$uploads, 'progress']);
$router->post('/uploads/{id}/fail', [$uploads, 'fail']);
$router->get('/uploads/status', [$uploads, 'status']);
$router->get('/sflex-media/{type}/{date}/{file}', [$sflex, 'media']);
$router->post('/sflex', [$sflex, 'create']);
$router->post('/sflex/{id}/react', [$sflex, 'react']);
$router->post('/sflex/{id}/comment', [$sflex, 'comment']);
$router->post('/sflex/comments/{id}/delete', [$sflex, 'deleteComment']);
$router->get('/sflex/{id}/comments', [$sflex, 'comments']);
$router->get('/sflex/{id}/reactions', [$sflex, 'reactions']);
$router->post('/sflex/comments/{id}/moderate', [$sflex, 'moderateComment']);
$router->post('/sflex/{id}/review', [$sflex, 'review']);
$router->post('/events', [$events, 'store']);
$router->post('/events/{id}/review', [$events, 'review']);
$router->post('/events/{id}/edit', [$events, 'update']);
$router->post('/events/{id}/delete', [$events, 'delete']);
$router->post('/organizers/{id}', [$events, 'organizerUpdate']);

$materialRequests = new MaterialRequestController();
$router->get('/material-requests', [$pages, 'materialRequests']);
$router->get('/material-requests/{id}', [$pages, 'materialRequestDetail']);
$router->post('/events/{id}/material-request', [$materialRequests, 'store']);
$router->post('/material-requests/{id}', [$materialRequests, 'update']);


$application = new ApplicationController();
$router->any('/', [$application, 'dispatch']);
$router->any('/{path}', [$application, 'dispatch']);
