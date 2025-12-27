<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Queue;

class QueueRepository
{
    /** @var array<int, Queue> */
    private array $queues;

    public function __construct()
    {
        $this->queues = [
            1 => new Queue(1, 'Atendimento Geral', 'A', 'fifo'),
            2 => new Queue(2, 'Preferencial', 'P', 'priority'),
        ];
    }

    /** @return list<Queue> */
    public function all(): array
    {
        return array_values($this->queues);
    }

    public function find(int $id): ?Queue
    {
        return $this->queues[$id] ?? null;
    }

    public function listByScope(?string $scope = null): array
    {
        if (!$scope) {
            return $this->all();
        }

        return array_values(array_filter($this->queues, static fn (Queue $queue) => $queue->scope === $scope));
    }

    public function updatePolicy(array $data): bool
    {
        $id = (int) ($data['queue_id'] ?? 0);
        if (!isset($this->queues[$id])) {
            return false;
        }

        $this->queues[$id]->policy = (string) ($data['policy'] ?? 'fifo');
        return true;
    }
}
