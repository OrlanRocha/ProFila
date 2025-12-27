<?php

declare(strict_types=1);

namespace App\Models;

class Queue
{
    public function __construct(
        public int $id,
        public string $name,
        public string $letter,
        public string $policy,
        public ?int $serviceId = null,
        public ?string $scope = null,
        public bool $active = true
    ) {
    }
}
