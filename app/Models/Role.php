<?php

declare(strict_types=1);

namespace App\Models;

class Role
{
    public function __construct(
        public int $id,
        public string $name,
        /** @var list<string> */
        public array $permissions = []
    ) {
    }
}
