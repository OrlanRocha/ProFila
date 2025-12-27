<?php

declare(strict_types=1);

use App\Controllers\Api\MonitorController;
use App\Controllers\Api\QueueController;
use App\Controllers\Api\ReportController;
use App\Controllers\Api\TicketController;
use App\Controllers\Api\UserController;
use App\Controllers\Web\AuthController;
use App\Controllers\Web\InstallerController;
use App\Core\Request;
use App\Core\Router;
use App\Middlewares\AuthMiddleware;

return function (Router $router, array $services): void {
    $auth = [AuthMiddleware::class];

    $router->post('/api/auth/login', function (Request $request) use ($services) {
        return (new AuthController($services['authService']))->login($request);
    });
    $router->post('/api/auth/register', function (Request $request) use ($services) {
        return (new AuthController($services['authService']))->register($request);
    });
    $router->post('/api/install/step', function (Request $request) use ($services) {
        return (new InstallerController($services['installerService']))->step($request);
    });

    $router->post('/api/auth/logout', fn () => (new AuthController($services['authService']))->logout());

    $router->post('/api/tickets/create', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->create($request);
    }, $auth);
    $router->post('/api/tickets/next', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->next($request);
    }, $auth);
    $router->post('/api/tickets/call', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->call($request);
    }, $auth);
    $router->post('/api/tickets/start', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->start($request);
    }, $auth);
    $router->post('/api/tickets/finish', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->finish($request);
    }, $auth);
    $router->post('/api/tickets/cancel', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->cancel($request);
    }, $auth);
    $router->post('/api/tickets/transfer', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->transfer($request);
    }, $auth);
    $router->get('/api/tickets/live', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->live($request);
    }, $auth);

    $router->get('/api/queues', function (Request $request) use ($services) {
        return (new QueueController($services['queuePolicyService']))->index($request);
    }, $auth);
    $router->post('/api/queues/update-policy', function (Request $request) use ($services) {
        return (new QueueController($services['queuePolicyService']))->updatePolicy($request);
    }, $auth);

    $router->post('/api/monitors/register', function (Request $request) use ($services) {
        return (new MonitorController($services['monitorService']))->register($request);
    }, $auth);
    $router->post('/api/monitors/activate', function (Request $request) use ($services) {
        return (new MonitorController($services['monitorService']))->activate($request);
    }, $auth);

    $router->get('/api/reports/tma', function (Request $request) use ($services) {
        return (new ReportController($services['reportService']))->tma($request);
    }, $auth);
    $router->get('/api/reports/tme', function (Request $request) use ($services) {
        return (new ReportController($services['reportService']))->tme($request);
    }, $auth);
    $router->get('/api/reports/realtime', function (Request $request) use ($services) {
        return (new ReportController($services['reportService']))->realtime($request);
    }, $auth);

    $router->get('/api/users', fn (Request $request) => (new UserController($services['userRepository'], $services['userService']))->index(), $auth);
    $router->post('/api/users/create', function (Request $request) use ($services) {
        return (new UserController($services['userRepository'], $services['userService']))->create($request);
    }, $auth);
    $router->post('/api/users/update', function (Request $request) use ($services) {
        return (new UserController($services['userRepository'], $services['userService']))->update($request);
    }, $auth);
    $router->post('/api/users/toggle', function (Request $request) use ($services) {
        return (new UserController($services['userRepository'], $services['userService']))->toggle($request);
    }, $auth);
    $router->post('/api/users/reset-password', function (Request $request) use ($services) {
        return (new UserController($services['userRepository'], $services['userService']))->resetPassword($request);
    }, $auth);
};
