<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth as CoreAuth;
use App\Core\Validator;
use App\Repositories\UserRepository;

class AuthService
{
    private CoreAuth $auth;
    private UserRepository $users;
    public function __construct(UserRepository $users)
    {
        $this->auth = new CoreAuth($users);
        $this->users = $users;
    }

    /**
     * @return array{ok:bool,msg?:string}
     */
    public function login(string $email, string $password): array
    {
        if ($this->auth->isLocked($email)) {
            return ['ok' => false, 'msg' => 'Muitas tentativas. Aguarde alguns minutos e tente novamente.'];
        }

        $ok = $this->auth->attempt($email, $password);
        return $ok ? ['ok' => true, 'msg' => 'Login realizado'] : ['ok' => false, 'msg' => 'Credenciais inválidas'];
    }

    public function logout(): void
    {
        $this->auth->logout();
    }

    public function register(array $data): array
    {
        $errors = Validator::validate($data, [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        if ($this->users->findByEmail($data['email'])) {
            return ['ok' => false, 'msg' => 'E-mail já cadastrado'];
        }

        $data['role_id'] = $data['role_id'] ?? 4;
        $this->users->create($data);

        return ['ok' => true, 'msg' => 'Usuário criado com sucesso'];
    }
}
