<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

abstract class BaseController
{
    protected function view(string $template, array $data = [], string $layout = 'app'): Response
    {
        return Response::view($template, $data, $layout);
    }

    protected function json(array $payload, int $status = 200): Response
    {
        return Response::json($payload, $status);
    }
}
