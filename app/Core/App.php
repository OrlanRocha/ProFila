<?php

declare(strict_types=1);

namespace App\Core;

use App\Repositories\QueueRepository;
use App\Repositories\ReportRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\InstallerService;
use App\Services\MonitorService;
use App\Services\QueuePolicyService;
use App\Services\ReportService;
use App\Services\TicketService;
use App\Services\UserService;
use Dotenv\Dotenv;
use Throwable;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->bootEnv();
        Session::start();
        $this->router = new Router();
        $this->registerRoutes();
    }

    public function run(): void
    {
        try {
            $request = Request::capture();
            $response = $this->router->dispatch($request);
            $response->send();
        } catch (Throwable $e) {
            Logger::log('error', $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Response::json(['ok' => false, 'msg' => 'Erro interno'])->send();
        }
    }

    private function bootEnv(): void
    {
        $envFile = __DIR__ . '/../../.env';
        if (class_exists(Dotenv::class) && is_file($envFile)) {
            Dotenv::createImmutable(dirname($envFile))->load();
            return;
        }

        if (!is_file($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            putenv($key . '=' . $value);
        }
    }

    private function registerRoutes(): void
    {
        $serviceContainer = $this->buildServices();

        $web = require __DIR__ . '/../../routes/web.php';
        $api = require __DIR__ . '/../../routes/api.php';

        $web($this->router, $serviceContainer);
        $api($this->router, $serviceContainer);
    }

    private function buildServices(): array
    {
        $roleRepository = new RoleRepository();
        $userRepository = new UserRepository($roleRepository);
        $ticketRepository = new TicketRepository();
        $queueRepository = new QueueRepository();
        $reportRepository = new ReportRepository($ticketRepository);

        $authService = new AuthService($userRepository);
        $ticketService = new TicketService($ticketRepository, $queueRepository);
        $queuePolicyService = new QueuePolicyService($queueRepository);
        $monitorService = new MonitorService($queueRepository, $ticketRepository);
        $reportService = new ReportService($reportRepository);
        $userService = new UserService($userRepository);
        $installerService = new InstallerService();

        return [
            'authService' => $authService,
            'ticketService' => $ticketService,
            'queuePolicyService' => $queuePolicyService,
            'monitorService' => $monitorService,
            'reportService' => $reportService,
            'userRepository' => $userRepository,
            'queueRepository' => $queueRepository,
            'userService' => $userService,
            'roleRepository' => $roleRepository,
            'installerService' => $installerService,
        ];
    }
}
