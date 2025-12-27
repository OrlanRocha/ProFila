<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $role,
        public string $passwordHash,
        public array $scopes = [],
        public bool $active = true,
        public ?string $cpf = null
    ) {
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
