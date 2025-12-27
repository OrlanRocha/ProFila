<?php

declare(strict_types=1);

use Websocket\WsServer;

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
}

// Garante autoload das classes Websocket/App mesmo sem Composer configurado para elas
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Websocket\\\\' => __DIR__ . '/',
        'App\\\\' => __DIR__ . '/../app/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $path = $baseDir . str_replace($prefix, '', $class);
            $path = str_replace('\\\\', '/', $path) . '.php';
            if (is_file($path)) {
                require $path;
            }
        }
    }
});

env();

$host = getenv('WS_HOST') ?: '127.0.0.1';
$port = (int) (getenv('WS_PORT') ?: 8080);

WsServer::run($host, $port);

function env(): void
{
    $envFile = __DIR__ . '/../.env';
    if (!is_file($envFile)) {
        return;
    }

    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#')) {
            continue;
        }

        if (!str_contains($line, '=')) {
            continue;
        }

        [$k, $v] = array_map('trim', explode('=', $line, 2));
        putenv($k . '=' . $v);
    }
}
