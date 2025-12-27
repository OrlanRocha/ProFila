<?php

declare(strict_types=1);

namespace App\Models;

class Permission
{
    public function __construct(
        public int $id,
        public string $key,
        public string $description
    ) {
    }
}
