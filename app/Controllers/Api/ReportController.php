<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\ReportService;

class ReportController extends BaseController
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function tma(Request $request): \App\Core\Response
    {
        return $this->json(['ok' => true, 'data' => $this->reportService->tma($request->all())]);
    }

    public function tme(Request $request): \App\Core\Response
    {
        return $this->json(['ok' => true, 'data' => $this->reportService->tme($request->all())]);
    }

    public function realtime(Request $request): \App\Core\Response
    {
        return $this->json(['ok' => true, 'data' => $this->reportService->realtime($request->all())]);
    }
}
