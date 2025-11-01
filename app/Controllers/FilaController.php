<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Fila;
use App\Models\Unidade;

class FilaController extends Controller
{
    private Fila $filas;
    private Unidade $unidades;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->filas = new Fila($config);
        $this->unidades = new Unidade($config);
    }

    public function index(): void
    {
        $this->requirePermission('filas.manage');
        $this->view('filas/index', [
            'filas' => $this->filas->all(),
            'unidades' => $this->unidades->all(),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function save(): void
    {
        $this->requirePermission('filas.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'unidade_id' => (int) ($_POST['unidade_id'] ?? 0) ?: null,
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'sigla' => strtoupper(substr((string) ($_POST['sigla'] ?? ''), 0, 5)),
            'prioridade_padrao' => (int) ($_POST['prioridade_padrao'] ?? 0),
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
        ];

        if ($id) {
            $this->filas->update($id, $data);
        } else {
            $this->filas->create($data);
        }

        $this->session->set('flash', 'Fila salva com sucesso.');
        $this->redirect('filas/index');
    }

    public function delete(): void
    {
        $this->requirePermission('filas.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $this->filas->delete($id);
        }
        $this->session->set('flash', 'Fila removida.');
        $this->redirect('filas/index');
    }
}
