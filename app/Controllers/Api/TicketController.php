<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Validator;
use App\Services\TicketService;

class TicketController extends BaseController
{
    public function __construct(private readonly TicketService $ticketService)
    {
    }

    public function create(Request $request): \App\Core\Response
    {
        $input = $request->all();
        $errors = Validator::validate($input, [
            'queue_id' => ['required'],
            'service_id' => ['required'],
        ]);

        if ($errors) {
            return $this->json(['ok' => false, 'msg' => 'Dados inválidos', 'errors' => $errors], 422);
        }

        $ticket = $this->ticketService->create($input);
        return $this->json(['ok' => true, 'data' => $ticket, 'msg' => 'Senha criada com sucesso']);
    }

    public function next(Request $request): \App\Core\Response
    {
        $queueId = (int) $request->input('queue_id');
        $pointId = $request->input('point_id');
        $ticket = $this->ticketService->next($queueId, $pointId !== null ? (int) $pointId : null);

        if (!$ticket) {
            return $this->json(['ok' => false, 'msg' => 'Nenhuma senha disponível'], 404);
        }

        return $this->json(['ok' => true, 'data' => $ticket]);
    }

    public function call(Request $request): \App\Core\Response
    {
        $ticketId = (int) $request->input('ticket_id');
        $ticket = $this->ticketService->call($ticketId, (int) $request->input('point_id'));

        return $ticket
            ? $this->json(['ok' => true, 'data' => $ticket, 'msg' => 'Senha chamada'])
            : $this->json(['ok' => false, 'msg' => 'Não foi possível chamar a senha'], 400);
    }

    public function start(Request $request): \App\Core\Response
    {
        $ticketId = (int) $request->input('ticket_id');
        $ticket = $this->ticketService->start($ticketId, (int) $request->input('point_id'));

        return $ticket
            ? $this->json(['ok' => true, 'data' => $ticket, 'msg' => 'Atendimento iniciado'])
            : $this->json(['ok' => false, 'msg' => 'Falha ao iniciar atendimento'], 400);
    }

    public function finish(Request $request): \App\Core\Response
    {
        $ticketId = (int) $request->input('ticket_id');
        $ticket = $this->ticketService->finish($ticketId, $request->input('result', 'DONE'));

        return $ticket
            ? $this->json(['ok' => true, 'data' => $ticket, 'msg' => 'Atendimento concluído'])
            : $this->json(['ok' => false, 'msg' => 'Falha ao finalizar atendimento'], 400);
    }

    public function cancel(Request $request): \App\Core\Response
    {
        $ticketId = (int) $request->input('ticket_id');
        $reason = $request->input('motivo', 'Operação');

        return $this->ticketService->cancel($ticketId, $reason)
            ? $this->json(['ok' => true, 'msg' => 'Senha cancelada'])
            : $this->json(['ok' => false, 'msg' => 'Falha ao cancelar senha'], 400);
    }

    public function transfer(Request $request): \App\Core\Response
    {
        $ticketId = (int) $request->input('ticket_id');
        $queueId = (int) $request->input('queue_id');

        return $this->ticketService->transfer($ticketId, $queueId)
            ? $this->json(['ok' => true, 'msg' => 'Senha transferida'])
            : $this->json(['ok' => false, 'msg' => 'Falha ao transferir senha'], 400);
    }

    public function live(Request $request): \App\Core\Response
    {
        $queueId = (int) $request->input('queue_id');
        return $this->json(['ok' => true, 'data' => $this->ticketService->liveQueue($queueId)]);
    }
}
