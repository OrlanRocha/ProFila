<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    private Usuario $usuarios;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->usuarios = new Usuario($config);
    }

    public function index(): void
    {
        $this->requirePermission('usuarios.manage');
        $users = $this->usuarios->all();
        $this->view('usuarios/index', [
            'usuarios' => $users,
            'token' => Csrf::token($this->session),
        ]);
    }

    public function form(): void
    {
        $this->requirePermission('usuarios.manage');
        $id = (int) ($_GET['id'] ?? 0);
        $usuario = $id ? $this->usuarios->find($id) : null;
        $this->view('usuarios/form', [
            'usuario' => $usuario,
            'token' => Csrf::token($this->session),
        ]);
    }

    public function save(): void
    {
        $this->requirePermission('usuarios.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'email' => filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'papel' => $_POST['papel'] ?? 'atendente',
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
        ];

        if (!$data['nome'] || !$data['email']) {
            $this->session->set('flash', 'Preencha todos os campos obrigatórios.');
            $this->redirect('usuarios/index');
        }

        if ($id) {
            $this->usuarios->update($id, $data);
        } else {
            $senha = $_POST['senha'] ?? '';
            if (!$senha) {
                $senha = bin2hex(random_bytes(4));
            }
            $data['senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
            $this->usuarios->create($data);
        }

        $this->session->set('flash', 'Usuário salvo com sucesso.');
        $this->redirect('usuarios/index');
    }

    public function delete(): void
    {
        $this->requirePermission('usuarios.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $this->usuarios->delete($id);
        }
        $this->session->set('flash', 'Usuário removido.');
        $this->redirect('usuarios/index');
    }

    public function reset(): void
    {
        $this->requirePermission('usuarios.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $nova = bin2hex(random_bytes(4));
            $this->usuarios->updatePassword($id, password_hash($nova, PASSWORD_DEFAULT));
            $this->session->set('flash', 'Nova senha: ' . $nova);
        }

        $this->redirect('usuarios/index');
    }
}
