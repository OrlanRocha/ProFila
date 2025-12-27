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
}
