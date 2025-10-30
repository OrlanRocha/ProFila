<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Sector
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $statement = $this->connection->query('SELECT id, name, prefix FROM sectors WHERE is_active = 1 ORDER BY name');
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->connection->prepare('SELECT id, name, prefix FROM sectors WHERE id = :id AND is_active = 1 LIMIT 1');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $sector = $statement->fetch();

        return $sector ?: null;
    }
}
