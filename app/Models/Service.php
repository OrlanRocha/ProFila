<?php

declare(strict_types=1);

namespace App\Models;

class Service
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $groupId = null
    ) {
    }
}
