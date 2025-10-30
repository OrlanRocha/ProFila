<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\RelatorioService;

class DashboardController extends Controller
{
    private RelatorioService $relatorios;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->relatorios = new RelatorioService($config);
    }

    public function index(): void
    {
        $this->requireRole(['admin', 'gestor']);
        $indicadores = $this->relatorios->indicadoresDiarios();
        $porFila = $this->relatorios->volumePorFila();
        $porDia = $this->relatorios->volumePorDia();
        $prioridade = $this->relatorios->taxaPrioridade();

        $this->view('relatorios/dashboard', [
            'indicadores' => $indicadores,
            'porFila' => $porFila,
            'porDia' => $porDia,
            'prioridade' => $prioridade,
        ]);
    }
}
