<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\QueueRepository;
use App\Repositories\TicketRepository;

class TicketService
{
    public function __construct(
        private readonly TicketRepository $tickets,
        private readonly QueueRepository $queues
    ) {
    }

    public function create(array $data): Ticket
    {
        $queue = $this->queues->find((int) $data['queue_id']);
        $data['letter'] = $queue?->letter ?? 'A';
        return $this->tickets->create($data);
    }

    public function next(int $queueId, ?int $pointId): ?Ticket
    {
        $ticket = $this->tickets->nextWaiting($queueId);
        if (!$ticket) {
            return null;
        }

        $ticket->status = 'CALLED';
        $ticket->currentPointId = $pointId;
        $ticket->calledAt = new \DateTimeImmutable();
        $ticket->attempts++;
        $this->tickets->update($ticket);
        $this->tickets->logEvent($ticket, 'TICKET_CALLED', ['point_id' => $pointId]);

        return $ticket;
    }

    public function call(int $ticketId, int $pointId): ?Ticket
    {
        $ticket = $this->tickets->findById($ticketId);
        if (!$ticket) {
            return null;
        }

        $ticket->status = 'CALLED';
        $ticket->currentPointId = $pointId;
        $ticket->calledAt = new \DateTimeImmutable();
        $ticket->attempts++;
        $this->tickets->update($ticket);
        $this->tickets->logEvent($ticket, 'TICKET_RECALLED', ['point_id' => $pointId]);

        return $ticket;
    }

    public function start(int $ticketId, int $pointId): ?Ticket
    {
        $ticket = $this->tickets->findById($ticketId);
        if (!$ticket) {
            return null;
        }

        $ticket->status = 'IN_SERVICE';
        $ticket->startedAt = new \DateTimeImmutable();
        $ticket->currentPointId = $pointId;
        $this->tickets->update($ticket);
        $this->tickets->logEvent($ticket, 'TICKET_STARTED', ['point_id' => $pointId]);

        return $ticket;
    }

    public function finish(int $ticketId, string $result): ?Ticket
    {
        $ticket = $this->tickets->findById($ticketId);
        if (!$ticket) {
            return null;
        }

        $ticket->status = $result;
        $ticket->finishedAt = new \DateTimeImmutable();
        $this->tickets->update($ticket);
        $this->tickets->logEvent($ticket, 'TICKET_FINISHED', ['result' => $result]);

        return $ticket;
    }

    public function cancel(int $ticketId, string $reason): bool
    {
        $ticket = $this->tickets->findById($ticketId);
        if (!$ticket) {
            return false;
        }

        $ticket->status = 'CANCELED';
        $ticket->finishedAt = new \DateTimeImmutable();
        $this->tickets->update($ticket);
        $this->tickets->logEvent($ticket, 'TICKET_CANCELED', ['reason' => $reason]);

        return true;
    }

    public function transfer(int $ticketId, int $queueId): bool
    {
        $ticket = $this->tickets->findById($ticketId);
        if (!$ticket) {
            return false;
        }

        $ticket->queueId = $queueId;
        $ticket->status = 'TRANSFERRED';
        $this->tickets->update($ticket);
        $this->tickets->logEvent($ticket, 'TICKET_TRANSFERRED', ['queue_id' => $queueId]);

        return true;
    }

    /** @return list<Ticket> */
    public function liveQueue(int $queueId): array
    {
        return $this->tickets->liveQueue($queueId);
    }

    /** @return list<Ticket> */
    public function latestByChannel(string $channelId): array
    {
        return $this->tickets->latestByChannel($channelId);
    }

    /** @return array<int, list<Ticket>> */
    public function liveQueues(): array
    {
        return $this->tickets->liveQueues();
    }
}
