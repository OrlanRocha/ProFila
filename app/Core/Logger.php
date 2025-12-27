<?php

declare(strict_types=1);

namespace App\Core;

use DateTimeImmutable;

class Logger
{
    private const LEVELS = ['debug', 'info', 'warning', 'error'];

    public static function log(string $level, string $message, array $context = []): void
    {
        if (!in_array($level, self::LEVELS, true)) {
            $level = 'info';
        }

        $line = sprintf(
            '[%s] %s: %s %s%s',
            (new DateTimeImmutable())->format(DATE_ATOM),
            strtoupper($level),
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '',
            PHP_EOL
        );

        $logPath = __DIR__ . '/../../storage/logs/app.log';
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        file_put_contents($logPath, $line, FILE_APPEND);
    }
}
