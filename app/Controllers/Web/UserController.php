<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;

class UserController extends BaseController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserService $userService,
        private readonly RoleRepository $roles
    ) {
    }

    public function index(): \App\Core\Response
    {
        return $this->view('users/index', [
            'title' => 'Usuários',
        ]);
    }

    public function createForm(): \App\Core\Response
    {
        return $this->view('users/form', [
            'title' => 'Novo usuário',
            'mode' => 'create',
            'roles' => $this->roleOptions(),
        ]);
    }

    public function editForm(int $id): \App\Core\Response
    {
        $user = $this->users->findById($id);
        if (!$user) {
            return $this->view('errors/404', ['title' => 'Usuário não encontrado'], 'auth', 404);
        }

        return $this->view('users/form', [
            'title' => 'Editar usuário',
            'mode' => 'edit',
            'editUser' => [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
                'cpf' => $user->cpf,
                'ativo' => $user->active ? 1 : 0,
                'role_id' => $user->roleId,
            ],
            'roles' => $this->roleOptions(),
        ]);
    }

    /**
     * @return list<array{id:int,nome:string}>
     */
    private function roleOptions(): array
    {
        return array_map(
            static fn($role) => ['id' => $role->id, 'nome' => $role->name],
            $this->roles->allRoles()
        );
    }
}
