<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Core\Controller;
use Core\Session;

final class AuthController extends Controller
{
    public function showLoginForm(): void
    {
        if (Session::has('user')) {
            $this->redirect('/dashboard');
        }

        $this->render('auth/login', [
            'title' => 'Acesso ao Sistema',
            'message' => Session::getFlash('message'),
            'errors' => Session::getFlash('errors'),
        ]);
    }

    public function login(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '';
        $password = $_POST['password'] ?? '';

        $errors = [];

        if ($email === '') {
            $errors[] = 'Informe um e-mail válido.';
        }

        if ($password === '') {
            $errors[] = 'Informe a senha.';
        }

        if ($errors) {
            Session::setFlash('errors', $errors);
            $this->redirect('/login');
        }

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';
        $authService = new AuthService($pdo);
        $user = $authService->attemptLogin($email, $password);

        if ($user === null) {
            Session::setFlash('errors', ['Credenciais inválidas.']);
            $this->redirect('/login');
        }

        Session::set('user', $user);
        Session::setFlash('message', 'Bem-vindo de volta, ' . $user['name'] . '!');

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Session::forget('user');
        Session::setFlash('message', 'Sessão encerrada com sucesso.');
        $this->redirect('/login');
    }
}
