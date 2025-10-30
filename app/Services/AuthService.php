<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use PDO;

final class AuthService
{
    private User $users;

    public function __construct(private PDO $connection)
    {
        $this->users = new User($connection);
    }

    public function attemptLogin(string $email, string $password): ?array
    {
        $user = $this->users->findByEmail($email);

        if (! $user || (int) $user['is_active'] !== 1) {
            return null;
        }

        if (! password_verify($password, $user['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $this->users->updatePasswordHash((int) $user['id'], $newHash);
        }

        $this->users->registerSuccessfulLogin((int) $user['id']);

        unset($user['password_hash']);

        return $user;
    }
}
