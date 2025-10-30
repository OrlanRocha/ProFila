<?php

declare(strict_types=1);

namespace Core;

use App\Controllers\AtendimentoController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

final class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registerRoutes();
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $this->router->dispatch($method, $uri);
    }

    private function registerRoutes(): void
    {
        $this->router->get('/', [DashboardController::class, 'index']);

        $this->router->get('/login', [AuthController::class, 'showLoginForm']);
        $this->router->post('/login', [AuthController::class, 'login']);
        $this->router->post('/logout', [AuthController::class, 'logout']);

        $this->router->get('/dashboard', [DashboardController::class, 'index']);

        $this->router->get('/tickets', [AtendimentoController::class, 'index']);
        $this->router->post('/tickets', [AtendimentoController::class, 'store']);
        $this->router->post('/tickets/call', [AtendimentoController::class, 'callNext']);
        $this->router->post('/tickets/finish', [AtendimentoController::class, 'finish']);
        $this->router->get('/api/counters', [AtendimentoController::class, 'listCounters']);
    }
}
