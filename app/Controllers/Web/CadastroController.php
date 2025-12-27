<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Repositories\QueueRepository;
use App\Repositories\UserRepository;

class CadastroController extends BaseController
{
    public function __construct(
        private readonly QueueRepository $queueRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function index(): \App\Core\Response
    {
        return $this->view('cadastro/index', [
            'title' => 'Cadastros',
            'queues' => $this->queueRepository->all(),
            'users' => $this->userRepository->all(),
        ]);
    }
}
