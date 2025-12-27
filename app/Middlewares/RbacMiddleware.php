<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class RbacMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly array $roles = [])
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        if (!$this->roles) {
            return $next($request);
        }

        $role = Session::get('user_role');
        if (!in_array($role, $this->roles, true)) {
            return Response::json(['ok' => false, 'msg' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
