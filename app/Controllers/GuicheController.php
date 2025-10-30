<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Guiche;
use App\Models\Fila;

class GuicheController extends Controller
{
    private Guiche $guiches;
    private Fila $filas;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->guiches = new Guiche($config);
        $this->filas = new Fila($config);
    }

    public function index(): void
    {
        $this->requireRole(['admin', 'gestor']);
        $this->view('guiches/index', [
            'guiches' => $this->guiches->all(),
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
            'numero' => (int) ($_POST['numero'] ?? 0),
            'apelido' => trim((string) ($_POST['apelido'] ?? '')),
            'fila_padrao_id' => (int) ($_POST['fila_padrao_id'] ?? 0) ?: null,
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
        ];

        if ($id) {
            $this->guiches->update($id, $data);
        } else {
            $this->guiches->create($data);
        }

        $this->session->set('flash', 'Guichê salvo com sucesso.');
        $this->redirect('guiches/index');
    }

    public function delete(): void
    {
        $this->requireRole(['admin', 'gestor']);
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
