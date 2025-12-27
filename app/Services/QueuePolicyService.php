<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\QueueRepository;

class QueuePolicyService
{
    public function __construct(private readonly QueueRepository $queues)
    {
    }

    public function listQueues(?string $scope = null): array
    {
        return $this->queues->listByScope($scope);
    }

    public function updatePolicy(array $data): bool
    {
        return $this->queues->updatePolicy($data);
    }
}
