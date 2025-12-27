<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Services\QueuePolicyService;
use App\Services\TicketService;

class AtendimentoController extends BaseController
{
    public function __construct(
        private readonly TicketService $ticketService,
        private readonly QueuePolicyService $queuePolicyService
    ) {
    }

    public function index(): \App\Core\Response
    {
        return $this->view('atendimento/index', [
            'title' => 'Atendimento',
            'queues' => $this->queuePolicyService->listQueues(),
        ]);
    }

    public function monitor(string $channelId): \App\Core\Response
    {
        $channelState = $this->ticketService->latestByChannel($channelId);
        return $this->view('monitor/index', [
            'title' => 'Painel em tempo real',
            'channelId' => $channelId,
            'channelState' => $channelState,
        ]);
    }
}
