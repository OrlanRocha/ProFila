<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Validator;
use App\Services\AuthService;

class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function showLogin(): \App\Core\Response
    {
        return $this->view('auth/login', ['title' => 'Entrar'], 'auth');
    }

    public function login(Request $request): \App\Core\Response
    {
        $input = $request->all();
        $errors = Validator::validate($input, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($errors) {
            return $this->json(['ok' => false, 'msg' => 'Dados inválidos', 'errors' => $errors], 422);
        }

        $result = $this->authService->login($input['email'], $input['password']);
        return $this->json($result, $result['ok'] ? 200 : 401);
    }

    public function loginWeb(Request $request): \App\Core\Response
    {
        $input = $request->all();
        $errors = Validator::validate($input, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($errors) {
            Session::flash('error', 'Dados inválidos');
            return $this->redirect('/login');
        }

        $result = $this->authService->login($input['email'], $input['password']);
        if ($result['ok']) {
            Session::flash('success', $result['msg'] ?? 'Login realizado');
            return $this->redirect('/dashboards');
        }

        Session::flash('error', $result['msg'] ?? 'Falha no login');
        return $this->redirect('/login');
    }

    public function showRegister(): \App\Core\Response
    {
        return $this->view('auth/register', ['title' => 'Criar conta'], 'auth');
    }

    public function register(Request $request): \App\Core\Response
    {
        $result = $this->authService->register($request->all());
        return $this->json($result, $result['ok'] ? 200 : 400);
    }

    public function logout(): \App\Core\Response
    {
        $this->authService->logout();
        return $this->json(['ok' => true, 'msg' => 'Logout realizado']);
    }

    public function logoutWeb(): \App\Core\Response
    {
        $this->authService->logout();
        Session::flash('success', 'Sessão encerrada');
        return $this->redirect('/login');
    }
}
