<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use PDO;

final class DashboardService
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return array<string, int>
     */
    public function getTicketSummary(): array
    {
        $sql = 'SELECT status, COUNT(*) as total FROM tickets GROUP BY status';
        $statement = $this->connection->query($sql);

        $summary = [
            Ticket::STATUS_WAITING => 0,
            Ticket::STATUS_CALLED => 0,
            Ticket::STATUS_FINISHED => 0,
        ];

        foreach ($statement->fetchAll() as $row) {
            $status = $row['status'];
            if (isset($summary[$status])) {
                $summary[$status] = (int) $row['total'];
            }
        }

        return $summary;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRecentTickets(): array
    {
        $sql = 'SELECT t.code, t.priority, t.status, t.created_at, s.name as sector_name '
            . 'FROM tickets t '
            . 'INNER JOIN sectors s ON s.id = t.sector_id '
            . 'ORDER BY t.created_at DESC LIMIT 10';

        $statement = $this->connection->query($sql);
        return $statement->fetchAll();
    }
}
