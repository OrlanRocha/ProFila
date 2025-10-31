<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AgendamentoService;
use App\Models\Uo;
use App\Models\ServicoCategoria;

class AgendamentoController extends Controller
{
    private AgendamentoService $service;
    private Uo $uo;
    private ServicoCategoria $categorias;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->service = new AgendamentoService($config);
        $this->uo = new Uo($config);
        $this->categorias = new ServicoCategoria($config);
    }

    public function index(): void
    {
        $this->requirePermission('senhas.emit');
        $this->view('agendamentos/index', [
            'agendamentos' => $this->service->listar(),
            'servicos' => $this->service->servicosAtivos(),
            'unidades' => $this->service->unidades(),
            'categorias' => $this->categorias->all(),
            'uos' => $this->uo->all(),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function salvar(): void
    {
        $this->requirePermission('senhas.emit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $dados = [
            'nome' => trim((string) $_POST['nome']),
            'documento' => trim((string) ($_POST['documento'] ?? '')) ?: null,
            'contato' => trim((string) ($_POST['contato'] ?? '')) ?: null,
            'prioridade_tipo' => $_POST['prioridade_tipo'] ?? 'padrao',
            'categoria' => $_POST['categoria'] ?? null,
            'servico_id' => $_POST['servico_id'] ?? '',
            'unidade_id' => $_POST['unidade_id'] ?? '',
            'data_agendada' => $_POST['data_agendada'] ?? null,
            'hora_agendada' => $_POST['hora_agendada'] ?? null,
            'observacoes' => trim((string) ($_POST['observacoes'] ?? '')) ?: null,
            'origem' => $_POST['origem'] ?? 'interno',
        ];

        if ($dados['nome'] === '' || $dados['data_agendada'] === null || $dados['hora_agendada'] === null || $dados['servico_id'] === '') {
            $this->session->set('flash', 'Preencha nome, data, hora e serviço para agendar.');
            $this->redirect('agendamentos/index');
        }

        $this->service->criar($dados);
        $this->session->set('flash', 'Agendamento registrado com sucesso.');
        $this->redirect('agendamentos/index');
    }
}
