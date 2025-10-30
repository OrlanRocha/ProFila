<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DashboardService;
use Core\Controller;
use Core\Session;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';
        $dashboardService = new DashboardService($pdo);

        $summary = $dashboardService->getTicketSummary();
        $recentTickets = $dashboardService->getRecentTickets();

        $this->render('dashboard/index', [
            'title' => 'Painel Geral',
            'user' => $user,
            'summary' => $summary,
            'recentTickets' => $recentTickets,
            'message' => Session::getFlash('message'),
        ]);
    }
}
