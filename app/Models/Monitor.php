<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

class Monitor
{
    public function __construct(
        public int $id,
        public string $channelId,
        public string $ip,
        public bool $active,
        public DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
    }
}
