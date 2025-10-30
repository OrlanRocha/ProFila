<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class User
{
    public function __construct(private PDO $connection)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->connection->prepare('SELECT id, name, email, password_hash, role, is_active FROM users WHERE email = :email LIMIT 1');
        $statement->bindValue(':email', mb_strtolower($email));
        $statement->execute();

        $user = $statement->fetch();

        return $user ?: null;
    }

    public function updatePasswordHash(int $userId, string $hash): void
    {
        $statement = $this->connection->prepare('UPDATE users SET password_hash = :hash, updated_at = NOW() WHERE id = :id');
        $statement->bindValue(':hash', $hash);
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function registerSuccessfulLogin(int $userId): void
    {
        $statement = $this->connection->prepare('UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id');
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }
}
