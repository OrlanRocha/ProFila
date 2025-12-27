<?php

declare(strict_types=1);

namespace App\Models;

class Channel
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public bool $active = true
    ) {
    }
}
