<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Fila;

class FilaController extends Controller
{
    private Fila $filas;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->filas = new Fila($config);
    }

    public function index(): void
    {
        $this->requireRole(['admin', 'gestor']);
        $this->view('filas/index', [
            'filas' => $this->filas->all(),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function save(): void
    {
        $this->requireRole(['admin', 'gestor']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $data = [
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
        $this->requireRole(['admin', 'gestor']);
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
