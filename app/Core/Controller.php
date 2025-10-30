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
        echo View::render($template, array_merge($data, ['config' => $this->config]));
    }

    protected function redirect(string $route): void
    {
        $baseUrl = rtrim($this->config['app']['base_url'] ?? '/', '/');
        $location = $baseUrl . '/index.php?r=' . $route;
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
}
