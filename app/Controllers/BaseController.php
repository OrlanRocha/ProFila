<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

abstract class BaseController
{
    protected function view(string $template, array $data = [], string $layout = 'app', int $status = 200): Response
    {
        return Response::view($template, $data, $layout, $status);
    }

    protected function json(array $payload, int $status = 200): Response
    {
        return Response::json($payload, $status);
    }
}
