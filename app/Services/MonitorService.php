<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Monitor;
use App\Repositories\QueueRepository;
use App\Repositories\TicketRepository;
use DateTimeImmutable;

class MonitorService
{
    /** @var array<int, Monitor> */
    private array $monitors = [];

    public function __construct(
        private readonly QueueRepository $queues,
        private readonly TicketRepository $tickets
    ) {
    }

    public function register(?string $channelId, ?string $ip): Monitor
    {
        $id = count($this->monitors) + 1;
        $monitor = new Monitor($id, $channelId ?? 'default', $ip ?? '127.0.0.1', true, new DateTimeImmutable());
        $this->monitors[$id] = $monitor;
        return $monitor;
    }

    public function activate(int $monitorId): ?Monitor
    {
        if (!isset($this->monitors[$monitorId])) {
            return null;
        }

        $this->monitors[$monitorId]->active = true;
        return $this->monitors[$monitorId];
    }

    public function registeredMonitors(): array
    {
        return array_values($this->monitors);
    }

    public function feed(string $channelId): array
    {
        return $this->tickets->latestByChannel($channelId);
    }
}
