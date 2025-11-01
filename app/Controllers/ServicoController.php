<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Servico;
use App\Models\ServicoCategoria;

class ServicoController extends Controller
{
    private Servico $servicos;
    private ServicoCategoria $categorias;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->servicos = new Servico($config);
        $this->categorias = new ServicoCategoria($config);
    }

    public function index(): void
    {
        $this->requirePermission('filas.manage');
        $this->view('servicos/index', [
            'servicos' => $this->servicos->all(),
            'categorias' => $this->categorias->all(),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function salvar(): void
    {
        $this->requirePermission('filas.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        if (isset($_POST['tipo']) && $_POST['tipo'] === 'categoria') {
            $this->categorias->create([
                'nome' => trim((string) $_POST['nome']),
                'descricao' => trim((string) ($_POST['descricao'] ?? '')) ?: null,
                'ativo' => 1,
            ]);
            $this->session->set('flash', 'Categoria cadastrada com sucesso.');
            $this->redirect('servicos/index');
        }

        $this->servicos->create([
            'categoria_id' => $_POST['categoria_id'] ?? '',
            'nome' => trim((string) $_POST['nome']),
            'descricao' => trim((string) ($_POST['descricao'] ?? '')) ?: null,
            'duracao_minutos' => (int) ($_POST['duracao_minutos'] ?? 0),
            'ativo' => 1,
        ]);
        $this->session->set('flash', 'Serviço cadastrado com sucesso.');
        $this->redirect('servicos/index');
    }
}
