<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\UserService;
use App\Repositories\UserRepository;

class UserController extends BaseController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService
    )
    {
    }

    public function index(): \App\Core\Response
    {
        return $this->json(['ok' => true, 'data' => $this->userRepository->all()]);
    }

    public function create(Request $request): \App\Core\Response
    {
        $result = $this->userService->register($request->all());
        return $this->json($result, $result['ok'] ? 200 : 400);
    }

    public function update(Request $request): \App\Core\Response
    {
        $id = (int) $request->input('id');
        $result = $this->userService->update($id, $request->all());
        return $this->json($result, $result['ok'] ? 200 : 404);
    }

    public function toggle(Request $request): \App\Core\Response
    {
        $id = (int) $request->input('id');
        $active = (bool) $request->input('active', true);
        $user = $this->userRepository->findById($id);
        if (!$user) {
            return $this->json(['ok' => false, 'msg' => 'Usuário não encontrado'], 404);
        }
        $this->userRepository->update($id, ['active' => $active]);
        return $this->json(['ok' => true, 'msg' => 'Status atualizado']);
    }

    public function resetPassword(Request $request): \App\Core\Response
    {
        $id = (int) $request->input('id');
        $newPassword = $request->input('new_password', bin2hex(random_bytes(4)));
        $user = $this->userRepository->update($id, ['password' => $newPassword]);
        return $user
            ? $this->json(['ok' => true, 'msg' => 'Senha redefinida', 'new_password' => $newPassword])
            : $this->json(['ok' => false, 'msg' => 'Usuário não encontrado'], 404);
    }
}
