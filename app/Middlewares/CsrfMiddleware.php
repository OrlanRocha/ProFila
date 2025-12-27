<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if ((int) (getenv('CSRF_ENABLED') ?: 0) === 0) {
            return $next($request);
        }

        $token = $request->input('_token');
        $sessionToken = Session::get('_csrf');

        if (!$token || !$sessionToken || !hash_equals((string) $sessionToken, (string) $token)) {
            return Response::json(['ok' => false, 'msg' => 'CSRF inválido'], 419);
        }

        return $next($request);
    }
}
