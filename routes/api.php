<?php

declare(strict_types=1);

use App\Controllers\Api\MonitorController;
use App\Controllers\Api\QueueController;
use App\Controllers\Api\ReportController;
use App\Controllers\Api\TicketController;
use App\Controllers\Api\UserController;
use App\Controllers\Web\AuthController;
use App\Core\Request;
use App\Core\Router;

return function (Router $router, array $services): void {
    $router->post('/api/auth/login', function (Request $request) use ($services) {
        return (new AuthController($services['authService']))->login($request);
    });

    $router->post('/api/auth/logout', fn () => (new AuthController($services['authService']))->logout());

    $router->post('/api/tickets/create', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->create($request);
    });
    $router->post('/api/tickets/next', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->next($request);
    });
    $router->post('/api/tickets/call', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->call($request);
    });
    $router->post('/api/tickets/start', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->start($request);
    });
    $router->post('/api/tickets/finish', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->finish($request);
    });
    $router->post('/api/tickets/cancel', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->cancel($request);
    });
    $router->post('/api/tickets/transfer', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->transfer($request);
    });
    $router->get('/api/tickets/live', function (Request $request) use ($services) {
        return (new TicketController($services['ticketService']))->live($request);
    });

    $router->get('/api/queues', function (Request $request) use ($services) {
        return (new QueueController($services['queuePolicyService']))->index($request);
    });
    $router->post('/api/queues/update-policy', function (Request $request) use ($services) {
        return (new QueueController($services['queuePolicyService']))->updatePolicy($request);
    });

    $router->post('/api/monitors/register', function (Request $request) use ($services) {
        return (new MonitorController($services['monitorService']))->register($request);
    });
    $router->post('/api/monitors/activate', function (Request $request) use ($services) {
        return (new MonitorController($services['monitorService']))->activate($request);
    });

    $router->get('/api/reports/tma', function (Request $request) use ($services) {
        return (new ReportController($services['reportService']))->tma($request);
    });
    $router->get('/api/reports/tme', function (Request $request) use ($services) {
        return (new ReportController($services['reportService']))->tme($request);
    });
    $router->get('/api/reports/realtime', function (Request $request) use ($services) {
        return (new ReportController($services['reportService']))->realtime($request);
    });

    $router->get('/api/users', fn (Request $request) => (new UserController($services['userRepository']))->index());
};
