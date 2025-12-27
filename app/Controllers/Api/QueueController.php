<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\QueuePolicyService;

class QueueController extends BaseController
{
    public function __construct(private readonly QueuePolicyService $queuePolicyService)
    {
    }

    public function index(Request $request): \App\Core\Response
    {
        $queues = $this->queuePolicyService->listQueues($request->input('uoiv'));
        return $this->json(['ok' => true, 'data' => $queues]);
    }

    public function updatePolicy(Request $request): \App\Core\Response
    {
        $result = $this->queuePolicyService->updatePolicy($request->all());

        return $result
            ? $this->json(['ok' => true, 'msg' => 'Política atualizada'])
            : $this->json(['ok' => false, 'msg' => 'Falha ao atualizar política'], 400);
    }
}
