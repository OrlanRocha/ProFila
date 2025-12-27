<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:callable, middleware:array}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middleware);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->getMethod()) {
                continue;
            }

            $matches = [];
            if (!preg_match($route['pattern'], $request->getPath(), $matches)) {
                continue;
            }

            $params = array_filter(
                $matches,
                static fn ($key) => !is_int($key),
                ARRAY_FILTER_USE_KEY
            );

            $handler = $this->wrapMiddleware($route['handler'], $route['middleware']);
            $response = $handler($request, ...array_values($params));

            if (!$response instanceof Response) {
                throw new RuntimeException('Handlers devem retornar uma instância de Response.');
            }

            return $response;
        }

        return Response::json(['ok' => false, 'msg' => 'Rota não encontrada'], 404);
    }

    private function addRoute(string $method, string $pattern, callable $handler, array $middleware): void
    {
        $regex = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = [
            'method' => $method,
            'pattern' => $regex,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * @param callable $handler
     * @param array<int, class-string<MiddlewareInterface>> $middleware
     */
    private function wrapMiddleware(callable $handler, array $middleware): Closure
    {
        return array_reduce(
            array_reverse($middleware),
            static function (callable $next, string $middlewareClass): Closure {
                return static function (Request $request, ...$params) use ($next, $middlewareClass) {
                    $middleware = new $middlewareClass();

                    if (!$middleware instanceof MiddlewareInterface) {
                        throw new RuntimeException('Middleware inválido.');
                    }

                    return $middleware->handle($request, fn (Request $req) => $next($req, ...$params));
                };
            },
            static fn (Request $request, ...$params) => $handler($request, ...$params)
        );
    }
}
