<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Services\ReportService;

class DashboardController extends BaseController
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(): \App\Core\Response
    {
        $indicators = $this->reportService->summary();
        return $this->view('dashboards/index', [
            'title' => 'Dashboard',
            'indicators' => $indicators,
        ]);
    }
}
