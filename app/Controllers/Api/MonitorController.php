<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\MonitorService;

class MonitorController extends BaseController
{
    public function __construct(private readonly MonitorService $monitorService)
    {
    }

    public function register(Request $request): \App\Core\Response
    {
        $monitor = $this->monitorService->register($request->input('channel_id'), $request->input('ip'));
        return $this->json(['ok' => true, 'data' => $monitor, 'msg' => 'Monitor registrado']);
    }

    public function activate(Request $request): \App\Core\Response
    {
        $monitor = $this->monitorService->activate($request->input('monitor_id'));

        return $monitor
            ? $this->json(['ok' => true, 'data' => $monitor, 'msg' => 'Monitor ativado'])
            : $this->json(['ok' => false, 'msg' => 'Falha ao ativar monitor'], 400);
    }
}
