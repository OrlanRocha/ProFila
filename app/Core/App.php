<?php
declare(strict_types=1);

namespace App\Core;

use Throwable;

class App
{
    public function __construct(
        private readonly Router $router,
        private readonly array $config
    ) {
    }

    public function run(): void
    {
        try {
            $route = $this->resolveRequestedRoute();
            $this->router->dispatch($route);
        } catch (HttpNotFoundException $exception) {
            http_response_code(404);
            echo View::renderStatic('errors/404', ['message' => $exception->getMessage()]);
        } catch (HttpForbiddenException $exception) {
            http_response_code(403);
            echo View::renderStatic('errors/403', ['message' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            http_response_code(500);
            echo View::renderStatic('errors/500', [
                'message' => $exception->getMessage(),
                'trace' => $this->config['app']['debug'] ?? false ? $exception->getTraceAsString() : null,
            ]);
        }
    }

    private function resolveRequestedRoute(): string
    {
        if (!empty($_GET['r'])) {
            return (string) $_GET['r'];
        }

        if (!empty($_GET['path'])) {
            return (string) $_GET['path'];
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $basePath = UrlGenerator::basePath($this->config);
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = strtok($uri, '?') ?: '';
        $uri = trim($uri, '/');
        if ($uri === '' || $uri === 'index.php') {
            return 'dashboard/index';
        }

        return $uri;
    }
}
