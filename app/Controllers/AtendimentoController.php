<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Core\Controller;
use Core\Session;

final class AtendimentoController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';
        $ticketService = new TicketService($pdo);

        $sectorId = filter_input(INPUT_GET, 'sector', FILTER_VALIDATE_INT) ?: null;

        $sectors = $ticketService->listSectors();
        $counters = $sectorId ? $ticketService->listCountersBySector($sectorId) : [];
        $tickets = $ticketService->listTickets($sectorId);

        $this->render('tickets/index', [
            'title' => 'Gestão de Atendimentos',
            'user' => $user,
            'sectors' => $sectors,
            'counters' => $counters,
            'tickets' => $tickets,
            'selectedSector' => $sectorId,
            'message' => Session::getFlash('message'),
            'errors' => Session::getFlash('errors'),
            'priorities' => Ticket::PRIORITIES,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $sectorId = filter_input(INPUT_POST, 'sector_id', FILTER_VALIDATE_INT) ?: 0;
        $priority = $_POST['priority'] ?? Ticket::PRIORIDADE_PADRAO;

        $errors = [];

        if ($sectorId <= 0) {
            $errors[] = 'Selecione um setor válido.';
        }

        if (! in_array($priority, Ticket::PRIORITIES, true)) {
            $errors[] = 'Prioridade inválida.';
        }

        if ($errors) {
            Session::setFlash('errors', $errors);
            $this->redirect('/tickets');
        }

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';
        $ticketService = new TicketService($pdo);

        $ticket = $ticketService->createTicket($sectorId, $priority);

        Session::setFlash('message', 'Senha ' . $ticket['code'] . ' gerada com sucesso.');
        $this->redirect('/tickets?sector=' . $sectorId);
    }

    public function callNext(): void
    {
        $this->requireAuth();

        $sectorId = filter_input(INPUT_POST, 'sector_id', FILTER_VALIDATE_INT) ?: 0;
        $counterId = filter_input(INPUT_POST, 'counter_id', FILTER_VALIDATE_INT) ?: 0;

        $errors = [];

        if ($sectorId <= 0) {
            $errors[] = 'Selecione um setor.';
        }

        if ($counterId <= 0) {
            $errors[] = 'Selecione um guichê.';
        }

        if ($errors) {
            Session::setFlash('errors', $errors);
            $this->redirect('/tickets');
        }

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';
        $ticketService = new TicketService($pdo);

        $ticket = $ticketService->callNextTicket($sectorId, $counterId);

        if ($ticket === null) {
            Session::setFlash('errors', ['Não há senhas aguardando atendimento para este setor.']);
        } else {
            Session::setFlash('message', 'Senha ' . $ticket['code'] . ' direcionada ao guichê selecionado.');
        }

        $this->redirect('/tickets?sector=' . $sectorId);
    }

    public function finish(): void
    {
        $this->requireAuth();

        $ticketId = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT) ?: 0;

        if ($ticketId <= 0) {
            Session::setFlash('errors', ['Informe uma senha válida.']);
            $this->redirect('/tickets');
        }

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';
        $ticketService = new TicketService($pdo);

        $ticketService->finishTicket($ticketId);

        Session::setFlash('message', 'Atendimento finalizado com sucesso.');
        $this->redirect('/tickets');
    }

    public function listCounters(): void
    {
        $this->requireAuth();

        $sectorId = filter_input(INPUT_GET, 'sector', FILTER_VALIDATE_INT);
        if (! $sectorId) {
            $this->respondJson([]);
            return;
        }

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';
        $ticketService = new TicketService($pdo);
        $counters = $ticketService->listCountersBySector($sectorId);

        $this->respondJson($counters);
    }

    private function respondJson(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
