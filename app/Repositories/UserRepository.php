<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;
use App\Models\User;
use PDO;

class UserRepository
{
    /** @return list<User> */
    public function all(): array
    {
        $pdo = DB::connection();
        $stmt = $pdo->query('SELECT id, name, email, role, password_hash, scopes FROM users LIMIT 50');
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        if (!$rows) {
            return [new User(1, 'Admin', 'admin@local', 'admin', password_hash('secret', PASSWORD_DEFAULT), ['*'])];
        }

        return array_map(function (array $row): User {
            $scopes = $row['scopes'] ? json_decode((string) $row['scopes'], true, 512, JSON_THROW_ON_ERROR) : [];
            return new User((int) $row['id'], (string) $row['name'], (string) $row['email'], (string) $row['role'], (string) $row['password_hash'], $scopes);
        }, $rows);
    }

    public function findByEmail(string $email): ?User
    {
        $pdo = DB::connection();
        $stmt = $pdo->prepare('SELECT id, name, email, role, password_hash, scopes FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $scopes = $row['scopes'] ? json_decode((string) $row['scopes'], true, 512, JSON_THROW_ON_ERROR) : [];
        return new User((int) $row['id'], (string) $row['name'], (string) $row['email'], (string) $row['role'], (string) $row['password_hash'], $scopes);
    }

    public function findById(int $id): ?User
    {
        $pdo = DB::connection();
        $stmt = $pdo->prepare('SELECT id, name, email, role, password_hash, scopes FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $scopes = $row['scopes'] ? json_decode((string) $row['scopes'], true, 512, JSON_THROW_ON_ERROR) : [];
        return new User((int) $row['id'], (string) $row['name'], (string) $row['email'], (string) $row['role'], (string) $row['password_hash'], $scopes);
    }
}
