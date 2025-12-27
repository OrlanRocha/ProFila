<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth as CoreAuth;
use App\Repositories\UserRepository;

class AuthService
{
    private CoreAuth $auth;

    public function __construct(UserRepository $users)
    {
        $this->auth = new CoreAuth($users);
    }

    public function login(string $email, string $password): bool
    {
        return $this->auth->attempt($email, $password);
    }

    public function logout(): void
    {
        $this->auth->logout();
    }
}
