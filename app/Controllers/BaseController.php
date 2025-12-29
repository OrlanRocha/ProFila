<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\Session;

abstract class BaseController
{
    protected function view(string $template, array $data = [], string $layout = 'app', int $status = 200): Response
    {
        $merged = $this->withGlobals($data);
        return Response::view($template, $merged, $layout, $status);
    }

    protected function json(array $payload, int $status = 200): Response
    {
        return Response::json($payload, $status);
    }

    protected function redirect(string $location, int $status = 302): Response
    {
        return Response::redirect($location, $status);
    }

    private function withGlobals(array $data): array
    {
        $flash = Session::pullFlash();
        $app = [
            'baseUrl' => getenv('APP_URL') ?: '',
            'wsUrl' => getenv('WS_URL') ?: '',
            'csrf' => Session::csrfToken(),
            'env' => getenv('APP_ENV') ?: 'local',
        ];

        $user = [
            'nome' => Session::get('user_name') ?? 'Usuário',
            'email' => Session::get('user_email') ?? '',
        ];

        $defaults = [
            'app' => $app,
            'user' => $user,
            'permissions' => Session::get('user_permissions', Session::get('user_scopes', [])),
            'flash' => $flash,
        ];

        return array_replace_recursive($defaults, $data);
    }
}
