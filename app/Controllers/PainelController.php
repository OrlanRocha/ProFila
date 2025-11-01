<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SenhaService;
use App\Core\Csrf;
use App\Models\PainelDisplay;
use App\Models\PainelRegra;
use App\Models\Unidade;
use App\Models\Orgao;
use App\Models\Cliente;
use App\Models\Uo;

class PainelController extends Controller
{
    private SenhaService $service;
    private PainelDisplay $displays;
    private PainelRegra $regras;
    private Unidade $unidades;
    private Orgao $orgaos;
    private Cliente $clientes;
    private Uo $uo;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->service = new SenhaService($config);
        $this->displays = new PainelDisplay($config);
        $this->regras = new PainelRegra($config);
        $this->unidades = new Unidade($config);
        $this->orgaos = new Orgao($config);
        $this->clientes = new Cliente($config);
        $this->uo = new Uo($config);
    }

    public function display(): void
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        $display = $this->displays->registrarPorIp($ip);
        $unidadeId = isset($display['unidade_id']) ? (int) $display['unidade_id'] : null;
        $ultima = $this->service->ultimaChamadaPainel($unidadeId);
        $historico = $this->service->historicoPainel($unidadeId);

        $this->view('painel/display', [
            'ultima' => $ultima,
            'historico' => $historico,
            'sse' => $this->config['app']['sse_enabled'] ?? true,
            'pollInterval' => (int) ($this->config['app']['poll_interval_ms'] ?? 5000),
            'display' => $display,
            'ip' => $ip,
        ]);
    }

    public function index(): void
    {
        $this->requirePermission('painel.manage');
        $this->view('painel/gestao', [
            'displays' => $this->displays->listarTodos(),
            'unidades' => $this->unidades->all(),
            'orgaos' => $this->orgaos->all(),
            'clientes' => $this->clientes->all(),
            'regras' => $this->regras->all(),
            'uoI' => $this->uo->porNivel('I'),
            'uoII' => $this->uo->porNivel('II'),
            'uoIII' => $this->uo->porNivel('III'),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function salvarDisplay(): void
    {
        $this->requirePermission('painel.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }
        $id = (int) ($_POST['display_id'] ?? 0);
        $unidade = $_POST['unidade_id'] !== '' ? (int) $_POST['unidade_id'] : null;
        $regra = $_POST['regra_id'] !== '' ? (int) $_POST['regra_id'] : null;
        $status = $_POST['status'] ?? 'pendente';
        $apelido = trim((string) ($_POST['apelido'] ?? '')) ?: null;
        if ($id > 0) {
            $this->displays->atualizarConfiguracao($id, $unidade, $regra, $status, $apelido);
            $this->session->set('flash', 'Display atualizado com sucesso.');
        }
        $this->redirect('painel/index');
    }

    public function criarContexto(): void
    {
        $this->requirePermission('painel.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }
        $tipo = $_POST['tipo'] ?? '';
        switch ($tipo) {
            case 'orgao':
                $this->orgaos->create([
                    'nome' => trim((string) $_POST['nome']),
                    'sigla' => trim((string) $_POST['sigla']),
                    'ativo' => 1,
                ]);
                $this->session->set('flash', 'Órgão cadastrado com sucesso.');
                break;
            case 'cliente':
                $this->clientes->create([
                    'nome' => trim((string) $_POST['nome']),
                    'documento' => trim((string) $_POST['documento']),
                    'ativo' => 1,
                ]);
                $this->session->set('flash', 'Cliente cadastrado com sucesso.');
                break;
            case 'unidade':
                $this->unidades->create([
                    'orgao_id' => (int) $_POST['orgao_id'],
                    'cliente_id' => (int) $_POST['cliente_id'],
                    'nome' => trim((string) $_POST['nome']),
                    'codigo' => trim((string) $_POST['codigo']),
                    'ativo' => 1,
                    'uo_nivel_i_id' => $_POST['uo_nivel_i_id'] !== '' ? (int) $_POST['uo_nivel_i_id'] : null,
                    'uo_nivel_ii_id' => $_POST['uo_nivel_ii_id'] !== '' ? (int) $_POST['uo_nivel_ii_id'] : null,
                    'uo_nivel_iii_id' => $_POST['uo_nivel_iii_id'] !== '' ? (int) $_POST['uo_nivel_iii_id'] : null,
                ]);
                $this->session->set('flash', 'Unidade cadastrada com sucesso.');
                break;
            case 'regra':
                $config = [
                    'historico' => (int) ($_POST['historico'] ?? 3),
                    'tema' => $_POST['tema'] ?? 'claro',
                    'polling' => (int) ($_POST['polling'] ?? 3000),
                ];
                $this->regras->create([
                    'nome' => trim((string) $_POST['nome']),
                    'descricao' => trim((string) $_POST['descricao']),
                    'configuracao' => $config,
                    'ativo' => 1,
                ]);
                $this->session->set('flash', 'Regra de painel criada com sucesso.');
                break;
        }
        $this->redirect('painel/index');
    }
}
