<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SenhaService;

class ApiController extends Controller
{
    private SenhaService $service;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->service = new SenhaService($config);
    }

    public function painelLast(): void
    {
        $ultimo = $this->service->ultimaChamadaPainel();
        header('Content-Type: application/json');
        echo json_encode(['data' => $ultimo]);
    }
}
