<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

class TicketEvent
{
    public function __construct(
        public int $id,
        public int $ticketId,
        public string $type,
        public array $payload = [],
        public ?int $userId = null,
        public ?int $pointId = null,
        public DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
    }
}
