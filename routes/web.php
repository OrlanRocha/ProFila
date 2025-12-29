<?php

declare(strict_types=1);

use App\Controllers\Web\AtendimentoController;
use App\Controllers\Web\AuthController;
use App\Controllers\Web\CadastroController;
use App\Controllers\Web\ConfigController;
use App\Controllers\Web\DashboardController;
use App\Controllers\Web\GerenciamentoController;
use App\Controllers\Web\AuditoriaController;
use App\Controllers\Web\MonitoramentoController;
use App\Controllers\Web\FilaController;
use App\Controllers\Web\PrioridadeController;
use App\Controllers\Web\UnidadeOrganizacionalController;
use App\Controllers\Web\InstallerController;
use App\Controllers\Web\UserController;
use App\Core\Request;
use App\Core\Router;
use App\Middlewares\AuthMiddleware;

return function (Router $router, array $services): void {
    $auth = [AuthMiddleware::class];
    $router->get('/', fn (Request $request) => (new DashboardController($services['reportService']))->index(), $auth);
    $router->get('/login', fn (Request $request) => (new AuthController($services['authService']))->showLogin());
    $router->post('/login', fn (Request $request) => (new AuthController($services['authService']))->loginWeb($request));
    $router->get('/register', fn (Request $request) => (new AuthController($services['authService']))->showRegister());
    $router->get('/logout', fn () => (new AuthController($services['authService']))->logoutWeb());
    $router->post('/logout', fn () => (new AuthController($services['authService']))->logoutWeb());
    $router->get('/install', fn (Request $request) => (new InstallerController($services['installerService']))->show());
    $router->get('/atendimento', fn (Request $request) => (new AtendimentoController($services['ticketService'], $services['queuePolicyService']))->index(), $auth);
    $router->get('/atendimento/planilhado', fn (Request $request) => (new AtendimentoController($services['ticketService'], $services['queuePolicyService']))->planilhado(), $auth);
    $router->get('/atendimento/presencial', fn (Request $request) => (new AtendimentoController($services['ticketService'], $services['queuePolicyService']))->presencial(), $auth);
    $router->get('/atendimento/recepcao', fn (Request $request) => (new AtendimentoController($services['ticketService'], $services['queuePolicyService']))->recepcao(), $auth);
    $router->get('/monitor/{channelId}', fn (Request $request, string $channelId) => (new AtendimentoController($services['ticketService'], $services['queuePolicyService']))->monitor($channelId), $auth);
    $router->get('/dashboards', fn (Request $request) => (new DashboardController($services['reportService']))->index(), $auth);
    $router->get('/cadastro', fn (Request $request) => (new CadastroController($services['queueRepository'], $services['userRepository']))->index(), $auth);
    $router->get('/config', fn (Request $request) => (new ConfigController($services['monitorService']))->index(), $auth);
    $router->get('/gerenciamento', fn (Request $request) => (new GerenciamentoController($services['ticketService']))->index(), $auth);
    $router->get('/auditoria', fn (Request $request) => (new AuditoriaController())->index(), $auth);
    $router->get('/monitoramento', fn (Request $request) => (new MonitoramentoController())->index(), $auth);
    $router->get('/fila', fn (Request $request) => (new FilaController())->index(), $auth);
    $router->get('/prioridade', fn (Request $request) => (new PrioridadeController())->index(), $auth);
    $router->get('/unidades', fn (Request $request) => (new UnidadeOrganizacionalController())->index(), $auth);
    $router->get('/unidades/cadastro', fn (Request $request) => (new UnidadeOrganizacionalController())->cadastro(), $auth);
    $router->get('/unidades/editar', fn (Request $request) => (new UnidadeOrganizacionalController())->editar(), $auth);
    $router->get('/usuarios', fn (Request $request) => (new UserController($services['userRepository'], $services['userService'], $services['roleRepository']))->index(), $auth);
    $router->get('/usuarios/novo', fn (Request $request) => (new UserController($services['userRepository'], $services['userService'], $services['roleRepository']))->createForm(), $auth);
    $router->get('/usuarios/{id}/editar', fn (Request $request, string $id) => (new UserController($services['userRepository'], $services['userService'], $services['roleRepository']))->editForm((int) $id), $auth);
};
