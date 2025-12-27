<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (!Session::get('user_id')) {
            return Response::json(['ok' => false, 'msg' => 'Não autenticado'], 401);
        }

        return $next($request);
    }
}
