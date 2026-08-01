<?php
declare(strict_types=1);

// Development mode
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require dirname(__DIR__).'/app/Bootstrap.php';
require dirname(__DIR__).'/routes/web.php';

