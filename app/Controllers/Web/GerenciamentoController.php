<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Services\TicketService;

class GerenciamentoController extends BaseController
{
    public function __construct(private readonly TicketService $ticketService)
    {
    }

    public function index(): \App\Core\Response
    {
        return $this->view('dashboards/operacional', [
            'title' => 'Sala de Situação',
            'liveQueues' => $this->ticketService->liveQueues(),
        ]);
    }
}
