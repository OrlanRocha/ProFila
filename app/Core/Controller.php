<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected array $config;
    protected Session $session;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->session = new Session();
    }

    protected function view(string $template, array $data = []): void
    {
        $data['config'] = $this->config;
        $data['url'] = fn (string $route, array $params = []): string => UrlGenerator::make($route, $this->config, $params);
        echo View::render($template, $data);
    }

    protected function redirect(string $route, array $params = []): void
    {
        $location = UrlGenerator::make($route, $this->config, $params);
        header('Location: ' . $location);
        exit;
    }

    protected function requireRole(array $roles): void
    {
        $user = $this->session->get('user');
        if (!$user) {
            $this->redirect('auth/login');
        }

        if (!in_array($user['papel'], $roles, true)) {
            throw new HttpForbiddenException('Acesso negado.');
        }
    }

    protected function requirePermission(string $permission): void
    {
        $user = $this->session->get('user');
        if (!$user) {
            $this->redirect('auth/login');
        }

        $permissoes = $user['permissoes'] ?? [];
        if (!in_array($permission, $permissoes, true)) {
            throw new HttpForbiddenException('Permissão insuficiente.');
        }
    }
}
