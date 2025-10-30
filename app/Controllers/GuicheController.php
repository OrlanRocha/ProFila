<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Guiche;
use App\Models\Fila;
use App\Models\Unidade;

class GuicheController extends Controller
{
    private Guiche $guiches;
    private Fila $filas;
    private Unidade $unidades;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->guiches = new Guiche($config);
        $this->filas = new Fila($config);
        $this->unidades = new Unidade($config);
    }

    public function index(): void
    {
        $this->requirePermission('guiches.manage');
        $this->view('guiches/index', [
            'guiches' => $this->guiches->all(),
            'filas' => $this->filas->all(),
            'unidades' => $this->unidades->all(),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function save(): void
    {
        $this->requirePermission('guiches.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $prioridades = array_filter(array_map('strval', $_POST['prioridades'] ?? []));
        if (empty($prioridades)) {
            $prioridades = ['padrao'];
        }

        $data = [
            'unidade_id' => (int) ($_POST['unidade_id'] ?? 0) ?: null,
            'numero' => (int) ($_POST['numero'] ?? 0),
            'apelido' => trim((string) ($_POST['apelido'] ?? '')),
            'fila_padrao_id' => (int) ($_POST['fila_padrao_id'] ?? 0) ?: null,
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
            'modo_atendimento' => $_POST['modo_atendimento'] ?? 'fifo',
            'prioridades_config' => $prioridades,
        ];

        if ($id) {
            $this->guiches->update($id, $data);
        } else {
            $this->guiches->create($data);
        }

        $this->session->set('flash', 'Configurações do guichê atualizadas.');
        $this->redirect('guiches/index');
    }

    public function delete(): void
    {
        $this->requirePermission('guiches.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $this->guiches->delete($id);
        }

        $this->session->set('flash', 'Guichê removido.');
        $this->redirect('guiches/index');
    }
}
