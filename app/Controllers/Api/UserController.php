<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Repositories\UserRepository;

class UserController extends BaseController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function index(): \App\Core\Response
    {
        return $this->json(['ok' => true, 'data' => $this->userRepository->all()]);
    }
}
