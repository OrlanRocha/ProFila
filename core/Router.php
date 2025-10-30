<?php

declare(strict_types=1);

namespace Core;

use Closure;

final class Router
{
    /** @var array<string, array<string, Closure|array{0: string, 1: string}>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, Closure|array $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, Closure|array $handler): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?? '/');
        $method = strtoupper($method);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo 'Página não encontrada';
            return;
        }

        if ($handler instanceof Closure) {
            $handler();
            return;
        }

        [$controllerClass, $controllerMethod] = $handler;

        if (! class_exists($controllerClass)) {
            http_response_code(500);
            echo 'Controller não encontrado';
            return;
        }

        $controller = new $controllerClass();

        if (! method_exists($controller, $controllerMethod)) {
            http_response_code(500);
            echo 'Ação inválida';
            return;
        }

        $controller->{$controllerMethod}();
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        return rtrim($path, '/') ?: '/';
    }
}
