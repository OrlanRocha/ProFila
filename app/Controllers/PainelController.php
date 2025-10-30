<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SenhaService;

class PainelController extends Controller
{
    private SenhaService $service;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->service = new SenhaService($config);
    }

    public function display(): void
    {
        $ultima = $this->service->ultimaChamadaPainel();
        $historico = $this->service->historicoPainel();
        $this->view('painel/display', [
            'ultima' => $ultima,
            'historico' => $historico,
            'sse' => $this->config['app']['sse_enabled'] ?? true,
            'pollInterval' => (int) ($this->config['app']['poll_interval_ms'] ?? 5000),
        ]);
    }
}
