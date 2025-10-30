<?php

declare(strict_types=1);

namespace Core;

abstract class Controller
{
    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = []): void
    {
        View::render($template, $data);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    /**
     * @return array<string, mixed>
     */
    protected function requireAuth(): array
    {
        $user = Session::get('user');
        if (! $user) {
            Session::setFlash('message', 'Por favor, realize o login para continuar.');
            $this->redirect('/login');
        }

        return $user;
    }
}
