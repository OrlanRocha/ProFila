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

    public function planilhado(): \App\Core\Response
    {
        return $this->view('atendimento/planilhado', [
            'title' => 'Atendimento Planilhado',
        ]);
    }

    public function presencial(): \App\Core\Response
    {
        $orgaos = [
            ['id' => 1, 'nome' => 'Orgão Saúde'],
            ['id' => 2, 'nome' => 'Orgão Educação'],
        ];

        $pontos = [
            ['id' => 1, 'nome' => 'Guichê 01', 'orgao_id' => 1],
            ['id' => 2, 'nome' => 'Guichê 02', 'orgao_id' => 2],
        ];

        return $this->view('atendimento/presencial', [
            'title' => 'Atendimento Presencial',
            'orgaos' => $orgaos,
            'pontos' => $pontos,
            'queues' => $this->queuePolicyService->listQueues(),
        ]);
    }

    public function recepcao(): \App\Core\Response
    {
        $orgaos = [
            ['id' => 1, 'nome' => 'Orgão Saúde'],
            ['id' => 2, 'nome' => 'Orgão Educação'],
        ];

        return $this->view('atendimento/recepcao', [
            'title' => 'Recepção',
            'orgaos' => $orgaos,
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
