<?php

declare(strict_types=1);

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\\\';
        if (str_starts_with($class, $prefix)) {
            $path = __DIR__ . '/../app/' . str_replace($prefix, '', $class);
            $path = str_replace('\\\\', '/', $path) . '.php';
            if (is_file($path)) {
                require $path;
            }
        }
    });
}

use App\Core\App;

$app = new App();
$app->run();
