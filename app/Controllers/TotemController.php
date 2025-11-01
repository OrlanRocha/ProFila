<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AgendamentoService;
use App\Services\SenhaService;
use App\Models\ServicoCategoria;
use App\Core\View;

class TotemController extends Controller
{
    private AgendamentoService $agendamentos;
    private SenhaService $senhas;
    private ServicoCategoria $categorias;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->agendamentos = new AgendamentoService($config);
        $this->senhas = new SenhaService($config);
        $this->categorias = new ServicoCategoria($config);
    }

    public function index(): void
    {
        $flash = $this->session->get('flash');
        if ($flash) {
            $this->session->remove('flash');
        }

        echo View::renderStatic('totem/index', [
            'servicos' => $this->agendamentos->servicosAtivos(),
            'categorias' => $this->categorias->all(),
            'filas' => (new \App\Models\Fila($this->config))->all(),
            'token' => Csrf::token($this->session),
            'flash' => $flash,
            'session' => $this->session,
            'config' => $this->config,
        ]);
        return;
    }

    public function agendar(): void
    {
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
            'origem' => 'totem',
        ];

        if ($dados['nome'] === '' || $dados['data_agendada'] === null || $dados['hora_agendada'] === null || $dados['servico_id'] === '') {
            $this->session->set('flash', 'Informe nome, data, horário e serviço para o agendamento.');
            $this->redirect('totem/index');
        }

        $this->agendamentos->criar($dados);
        $this->session->set('flash', 'Agendamento registrado. Procure a recepção no horário marcado.');
        $this->redirect('totem/index');
    }

    public function atendimentoImediato(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $filaId = (int) ($_POST['fila_id'] ?? 0);
        $prioridade = $_POST['prioridade_tipo'] ?? 'padrao';

        if ($filaId <= 0) {
            $this->session->set('flash', 'Selecione uma fila válida.');
            $this->redirect('totem/index');
        }

        $senha = $this->senhas->emitir($filaId, $prioridade);
        $this->session->set('flash', 'Retire sua senha: ' . $senha['codigo']);
        $this->redirect('totem/index');
    }
}
