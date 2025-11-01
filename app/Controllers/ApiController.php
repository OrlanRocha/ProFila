<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SenhaService;
use App\Models\PainelDisplay;

class ApiController extends Controller
{
    private SenhaService $service;
    private PainelDisplay $displays;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->service = new SenhaService($config);
        $this->displays = new PainelDisplay($config);
    }

    public function painelLast(): void
    {
        $token = $_GET['display'] ?? '';
        $display = $token ? $this->displays->buscarPorToken($token) : null;
        $unidadeId = $display && !empty($display['unidade_id']) ? (int) $display['unidade_id'] : null;
        $ultimo = $this->service->ultimaChamadaPainel($unidadeId);
        header('Content-Type: application/json');
        echo json_encode(['data' => $ultimo, 'display' => $display ? ['status' => $display['status'], 'unidade' => $display['unidade_nome'] ?? null] : null]);
    }
}
