<?php
declare(strict_types=1);

use App\Core\App;
use App\Core\Router;
use App\Core\Session;

require_once __DIR__ . '/bootstrap.php';

$config = require __DIR__ . '/config/env.php';

date_default_timezone_set($config['app']['timezone'] ?? 'America/Sao_Paulo');

Session::start();

$router = new Router($config);

$app = new App($router, $config);
$app->run();
