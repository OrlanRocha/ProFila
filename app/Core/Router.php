<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\UsuarioController;
use App\Controllers\FilaController;
use App\Controllers\GuicheController;
use App\Controllers\SenhaController;
use App\Controllers\PainelController;
use App\Controllers\RelatorioController;
use App\Controllers\DashboardController;
use App\Controllers\ApiController;
use App\Controllers\PermissaoController;

class Router
{
    public function __construct(private readonly array $config)
    {
    }

    public function dispatch(string $route): void
    {
        [$controllerName, $action] = $this->parseRoute($route);

        $controller = $this->resolveController($controllerName);

        if (!method_exists($controller, $action)) {
            throw new HttpNotFoundException('Ação não encontrada.');
        }

        $controller->$action();
    }

    private function parseRoute(string $route): array
    {
        $route = trim($route, '/');
        if ($route === '') {
            $route = 'dashboard/index';
        }

        $parts = array_values(array_filter(explode('/', $route)));
        $controller = $parts[0] ?? 'dashboard';
        $action = $parts[1] ?? 'index';

        $action = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $action))));

        if (count($parts) > 2) {
            $additional = array_slice($parts, 2);
            foreach ($additional as $segment) {
                $normalized = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segment)));
                $action .= $normalized;
            }
        }

        return [$controller, $action];
    }

    private function resolveController(string $controllerName): Controller
    {
        return match ($controllerName) {
            'auth' => new AuthController($this->config),
            'usuarios' => new UsuarioController($this->config),
            'filas' => new FilaController($this->config),
            'guiches' => new GuicheController($this->config),
            'senhas' => new SenhaController($this->config),
            'painel' => new PainelController($this->config),
            'relatorios' => new RelatorioController($this->config),
            'dashboard' => new DashboardController($this->config),
            'api' => new ApiController($this->config),
            'permissoes' => new PermissaoController($this->config),
            default => new DashboardController($this->config),
        };
    }
}
