<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Services\MonitorService;

class ConfigController extends BaseController
{
    public function __construct(private readonly MonitorService $monitorService)
    {
    }

    public function index(): \App\Core\Response
    {
        return $this->view('configuracao/index', [
            'title' => 'Configurações',
            'monitors' => $this->monitorService->registeredMonitors(),
        ]);
    }
}
