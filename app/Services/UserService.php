<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Validator;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(private readonly UserRepository $users)
    {
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

        $user = $this->users->create($data);
        return ['ok' => true, 'user' => $user];
    }

    public function update(int $id, array $data): array
    {
        $user = $this->users->update($id, $data);
        return $user ? ['ok' => true, 'user' => $user] : ['ok' => false, 'msg' => 'Usuário não encontrado'];
    }

    public function list(): array
    {
        return $this->users->all();
    }
}
