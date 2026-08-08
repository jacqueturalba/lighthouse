<?php
declare(strict_types=1);

require dirname(__DIR__).'/app/Controllers/ApplicationController.php';
require dirname(__DIR__).'/app/Controllers/StorageController.php';
require dirname(__DIR__).'/app/Controllers/PageController.php';
require dirname(__DIR__).'/app/Controllers/PromotionKitRequestController.php';

$storage = new StorageController();
$router->get('/storage/{type}/{folder}/{file}', [$storage, 'show']);
$pages = new PageController();
$router->get('/', [$pages, 'home']);
$router->get('/press-releases', [$pages, 'pressReleases']);
$router->get('/promotion-kits', [$pages, 'promotionKits']);
$requests = new PromotionKitRequestController();
$router->post('/promotion-kits/{id}/request', [$requests, 'store']);
$router->post('/promotion-kit-requests/{id}/approve', [$requests, 'approve']);
$router->post('/promotion-kit-requests/{id}/disapprove', [$requests, 'disapprove']);
$router->get('/calendar', [$pages, 'placeholder']);
$router->get('/trainings', [$pages, 'placeholder']);
$router->get('/guidelines', [$pages, 'placeholder']);
$router->get('/static-1', [$pages, 'placeholder']);
$router->get('/static-2', [$pages, 'placeholder']);
$application = new ApplicationController();
$router->any('/', [$application, 'dispatch']);
$router->any('/{path}', [$application, 'dispatch']);
