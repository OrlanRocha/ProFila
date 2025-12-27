<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

class Ticket
{
    public function __construct(
        public int $id,
        public int $queueId,
        public int $number,
        public string $letter,
        public string $display,
        public string $status,
        public ?int $currentPointId = null,
        public ?int $serviceId = null,
        public int $attempts = 0,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $calledAt = null,
        public ?DateTimeImmutable $startedAt = null,
        public ?DateTimeImmutable $finishedAt = null,
        public array $meta = []
    ) {
        $this->createdAt = $createdAt ?: new DateTimeImmutable();
    }
}
