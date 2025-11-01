<?php
declare(strict_types=1);

// Basic autoload when composer is not available
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $baseDir = __DIR__ . '/app/';
    $relative = substr($class, strlen($prefix));
    $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);
    $file = $baseDir . $relative . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
