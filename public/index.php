<?php

declare(strict_types=1);

use Core\App;
use Core\Session;

require __DIR__ . '/../core/bootstrap.php';

Session::start();

$app = new App();
$app->run();
