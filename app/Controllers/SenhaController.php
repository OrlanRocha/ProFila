<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Fila;
use App\Models\Guiche;
use App\Services\SenhaService;

class SenhaController extends Controller
{
    private Fila $filas;
    private Guiche $guiches;
    private SenhaService $service;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->filas = new Fila($config);
        $this->guiches = new Guiche($config);
        $this->service = new SenhaService($config);
    }

    public function emitir(): void
    {
        $this->requireRole(['admin', 'gestor', 'atendente']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($this->session, $_POST['_token'] ?? null)) {
                throw new \InvalidArgumentException('Token inválido.');
            }

            $filaId = (int) ($_POST['fila_id'] ?? 0);
            $prioridade = ($_POST['tipo'] ?? 'normal') === 'prioridade';
            $resultado = $this->service->emitir($filaId, $prioridade);
            $this->session->set('flash', 'Senha emitida: ' . $resultado['codigo']);
            $this->redirect('senhas/emitir');
        }

        $this->view('senhas/emitir', [
            'filas' => $this->filas->all(),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function operacao(): void
    {
        $this->requireRole(['admin', 'gestor', 'atendente']);
        $historico = $this->service->historicoPainel();
        $this->view('senhas/operacao', [
            'guiches' => $this->guiches->all(),
            'filas' => $this->filas->all(),
            'historico' => $historico,
            'token' => Csrf::token($this->session),
        ]);
    }

    public function proxima(): void
    {
        $this->requireRole(['admin', 'gestor', 'atendente']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $guicheId = (int) ($_GET['guiche'] ?? 0);
        $filaId = isset($_POST['fila_id']) ? (int) $_POST['fila_id'] : null;
        $dados = $this->service->chamarProxima($guicheId, $filaId ?: null);
        if ($dados) {
            $this->session->set('flash', 'Chamado: ' . $dados['codigo']);
        } else {
            $this->session->set('flash', 'Nenhuma senha aguardando.');
        }
        $this->redirect('senhas/operacao');
    }

    public function rechamar(): void
    {
        $this->requireRole(['admin', 'gestor', 'atendente']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $guicheId = (int) ($_GET['guiche'] ?? 0);
        $dados = $this->service->rechamar($guicheId);
        $this->session->set('flash', $dados ? 'Rechamada: ' . $dados['codigo'] : 'Nenhuma senha para rechamar.');
        $this->redirect('senhas/operacao');
    }

    public function finalizar(): void
    {
        $this->requireRole(['admin', 'gestor', 'atendente']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $this->service->finalizar($id);
            $this->session->set('flash', 'Senha finalizada.');
        }
        $this->redirect('senhas/operacao');
    }

    public function transferir(): void
    {
        $this->requireRole(['admin', 'gestor']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $fila = (int) ($_POST['fila_destino'] ?? 0);
        if ($id && $fila) {
            $this->service->transferir($id, $fila);
            $this->session->set('flash', 'Senha transferida.');
        }
        $this->redirect('senhas/operacao');
    }
}
