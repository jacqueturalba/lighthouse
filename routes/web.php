<?php
declare(strict_types=1);

require dirname(__DIR__).'/app/Controllers/ApplicationController.php';

(new ApplicationController())->dispatch();
