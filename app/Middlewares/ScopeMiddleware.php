<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class ScopeMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ?string $scope = null)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        if ($this->scope === null) {
            return $next($request);
        }

        $scopes = Session::get('user_scopes', []);
        if (!in_array($this->scope, $scopes, true) && !in_array('*', $scopes, true)) {
            return Response::json(['ok' => false, 'msg' => 'Escopo não autorizado'], 403);
        }

        return $next($request);
    }
}
