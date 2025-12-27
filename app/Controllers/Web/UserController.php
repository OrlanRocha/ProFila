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
            'users' => $this->users->all(),
            'roles' => $this->roles->allRoles(),
        ]);
    }
}
