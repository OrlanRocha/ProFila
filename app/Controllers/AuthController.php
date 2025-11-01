<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->auth = new AuthService($config);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($this->session, $_POST['_token'] ?? null)) {
                $this->view('auth/login', ['error' => 'Token inválido.']);
                return;
            }

            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '';
            $senha = (string) ($_POST['senha'] ?? '');

            if ($this->auth->attempt($email, $senha)) {
                $this->redirect('dashboard/index');
                return;
            }

            $this->view('auth/login', [
                'error' => 'Credenciais inválidas ou conta bloqueada.',
            ]);
            return;
        }

        $this->view('auth/login', [
            'token' => Csrf::token($this->session),
        ]);
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('auth/login');
    }
}
