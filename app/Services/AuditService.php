<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;

class AuditService
{
    public function log(string $action, array $payload = []): void
    {
        Logger::log('info', 'AUDIT: ' . $action, $payload);
    }
}
