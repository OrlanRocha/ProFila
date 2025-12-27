<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\TicketRepository;

class ReportRepository
{
    public function __construct(private readonly TicketRepository $tickets)
    {
    }

    public function tma(array $filters): array
    {
        return [
            'avg_service_time' => 180,
            'filters' => $filters,
        ];
    }

    public function tme(array $filters): array
    {
        return [
            'avg_wait_time' => 120,
            'filters' => $filters,
        ];
    }

    public function realtime(array $filters): array
    {
        return [
            'waiting' => count($this->tickets->liveQueues()),
            'filters' => $filters,
        ];
    }
}
