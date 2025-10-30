<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Counter;
use App\Models\Sector;
use App\Models\Ticket;
use PDO;
use PDOException;

final class TicketService
{
    private Sector $sectors;
    private Counter $counters;

    public function __construct(private PDO $connection)
    {
        $this->sectors = new Sector($connection);
        $this->counters = new Counter($connection);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listSectors(): array
    {
        return $this->sectors->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listCountersBySector(int $sectorId): array
    {
        return $this->counters->listBySector($sectorId);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listTickets(?int $sectorId = null): array
    {
        $sql = 'SELECT t.id, t.code, t.priority, t.status, t.created_at, t.called_at, t.finished_at, '
            . 's.name AS sector_name, c.name AS counter_name '
            . 'FROM tickets t '
            . 'INNER JOIN sectors s ON s.id = t.sector_id '
            . 'LEFT JOIN counters c ON c.id = t.counter_id '
            . 'WHERE s.is_active = 1';

        if ($sectorId !== null) {
            $sql .= ' AND t.sector_id = :sectorId';
        }

        $sql .= ' ORDER BY t.created_at DESC LIMIT 50';

        $statement = $this->connection->prepare($sql);

        if ($sectorId !== null) {
            $statement->bindValue(':sectorId', $sectorId, PDO::PARAM_INT);
        }

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>
     */
    public function createTicket(int $sectorId, string $priority): array
    {
        $sector = $this->sectors->find($sectorId);

        if ($sector === null) {
            throw new PDOException('Setor inválido.');
        }

        $nextNumber = $this->getNextTicketNumber($sectorId);
        $code = sprintf('%s%03d', $sector['prefix'], $nextNumber);

        $statement = $this->connection->prepare(
            'INSERT INTO tickets (sector_id, number, code, priority, status, created_at) '
            . 'VALUES (:sector, :number, :code, :priority, :status, NOW())'
        );
        $statement->bindValue(':sector', $sectorId, PDO::PARAM_INT);
        $statement->bindValue(':number', $nextNumber, PDO::PARAM_INT);
        $statement->bindValue(':code', $code);
        $statement->bindValue(':priority', $priority);
        $statement->bindValue(':status', Ticket::STATUS_WAITING);
        $statement->execute();

        $id = (int) $this->connection->lastInsertId();

        return [
            'id' => $id,
            'code' => $code,
            'priority' => $priority,
            'status' => Ticket::STATUS_WAITING,
        ];
    }

    public function callNextTicket(int $sectorId, int $counterId): ?array
    {
        try {
            $this->connection->beginTransaction();

            $statement = $this->connection->prepare(
                "SELECT id, code, priority FROM tickets WHERE sector_id = :sector AND status = :status "
                . "ORDER BY FIELD(priority, 'emergency','priority','normal'), created_at ASC LIMIT 1 FOR UPDATE"
            );
            $statement->bindValue(':sector', $sectorId, PDO::PARAM_INT);
            $statement->bindValue(':status', Ticket::STATUS_WAITING);
            $statement->execute();

            $ticket = $statement->fetch();

            if (! $ticket) {
                $this->connection->rollBack();
                return null;
            }

            $update = $this->connection->prepare(
                'UPDATE tickets SET status = :status, counter_id = :counter, called_at = NOW() WHERE id = :id'
            );
            $update->bindValue(':status', Ticket::STATUS_CALLED);
            $update->bindValue(':counter', $counterId, PDO::PARAM_INT);
            $update->bindValue(':id', (int) $ticket['id'], PDO::PARAM_INT);
            $update->execute();

            $this->connection->commit();

            return $ticket;
        } catch (PDOException $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function finishTicket(int $ticketId): void
    {
        $statement = $this->connection->prepare(
            'UPDATE tickets SET status = :status, finished_at = NOW() WHERE id = :id'
        );
        $statement->bindValue(':status', Ticket::STATUS_FINISHED);
        $statement->bindValue(':id', $ticketId, PDO::PARAM_INT);
        $statement->execute();
    }

    private function getNextTicketNumber(int $sectorId): int
    {
        $statement = $this->connection->prepare('SELECT MAX(number) AS max_number FROM tickets WHERE sector_id = :sector');
        $statement->bindValue(':sector', $sectorId, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch();
        $next = (int) ($result['max_number'] ?? 0) + 1;

        return $next;
    }
}
