<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;
use DateTimeImmutable;

class TicketRepository
{
    /** @var array<int, Ticket> */
    private static array $memoryStore = [];
    private static int $sequence = 1;

    public function create(array $data): Ticket
    {
        $id = self::$sequence++;
        $ticket = new Ticket(
            $id,
            (int) $data['queue_id'],
            number: self::$sequence,
            letter: (string) ($data['letter'] ?? 'A'),
            display: sprintf('%s-%03d', $data['letter'] ?? 'A', self::$sequence),
            status: 'WAITING',
            serviceId: isset($data['service_id']) ? (int) $data['service_id'] : null,
            meta: $data['meta'] ?? []
        );

        self::$memoryStore[$id] = $ticket;
        return $ticket;
    }

    public function findById(int $id): ?Ticket
    {
        return self::$memoryStore[$id] ?? null;
    }

    public function nextWaiting(int $queueId): ?Ticket
    {
        foreach (self::$memoryStore as $ticket) {
            if ($ticket->queueId === $queueId && $ticket->status === 'WAITING') {
                return $ticket;
            }
        }

        return null;
    }

    public function update(Ticket $ticket): void
    {
        self::$memoryStore[$ticket->id] = $ticket;
    }

    /** @return list<Ticket> */
    public function liveQueue(int $queueId): array
    {
        return array_values(array_filter(self::$memoryStore, static fn (Ticket $ticket) => $ticket->queueId === $queueId));
    }

    /** @return list<Ticket> */
    public function latestByChannel(string $channelId): array
    {
        return array_slice(array_values(self::$memoryStore), -5);
    }

    /** @return array<int, list<Ticket>> */
    public function liveQueues(): array
    {
        $grouped = [];
        foreach (self::$memoryStore as $ticket) {
            $grouped[$ticket->queueId][] = $ticket;
        }

        return $grouped;
    }

    public function logEvent(Ticket $ticket, string $type, array $payload = []): void
    {
        // Placeholder for persistence layer
        $payload['ticket_id'] = $ticket->id;
        $payload['type'] = $type;
        $payload['created_at'] = (new DateTimeImmutable())->format(DATE_ATOM);
    }
}
