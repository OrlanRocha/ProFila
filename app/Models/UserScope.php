<?php

declare(strict_types=1);

namespace App\Models;

class UserScope
{
    public function __construct(
        public int $userId,
        public int $uoiId,
        public ?int $uoiiId = null,
        public ?int $uoiiiId = null,
        public ?int $uoivId = null
    ) {
    }
}
