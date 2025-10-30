<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Counter
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listBySector(int $sectorId): array
    {
        $statement = $this->connection->prepare('SELECT id, name FROM counters WHERE sector_id = :sector AND is_active = 1 ORDER BY name');
        $statement->bindValue(':sector', $sectorId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
