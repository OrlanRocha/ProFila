<?php

declare(strict_types=1);

use App\Controllers\Web\AtendimentoController;
use App\Controllers\Web\AuthController;
use App\Controllers\Web\CadastroController;
use App\Controllers\Web\ConfigController;
use App\Controllers\Web\DashboardController;
use App\Controllers\Web\GerenciamentoController;
use App\Core\Request;
use App\Core\Router;

return function (Router $router, array $services): void {
    $router->get('/', fn (Request $request) => (new DashboardController($services['reportService']))->index());
    $router->get('/login', fn (Request $request) => (new AuthController($services['authService']))->showLogin());
    $router->get('/register', fn (Request $request) => (new AuthController($services['authService']))->showRegister());
    $router->get('/atendimento', fn (Request $request) => (new AtendimentoController($services['ticketService'], $services['queuePolicyService']))->index());
    $router->get('/monitor/{channelId}', fn (Request $request, string $channelId) => (new AtendimentoController($services['ticketService'], $services['queuePolicyService']))->monitor($channelId));
    $router->get('/dashboards', fn (Request $request) => (new DashboardController($services['reportService']))->index());
    $router->get('/cadastro', fn (Request $request) => (new CadastroController($services['queueRepository'], $services['userRepository']))->index());
    $router->get('/config', fn (Request $request) => (new ConfigController($services['monitorService']))->index());
    $router->get('/gerenciamento', fn (Request $request) => (new GerenciamentoController($services['ticketService']))->index());
};
