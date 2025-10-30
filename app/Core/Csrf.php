<?php
declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const TOKEN_KEY = '_csrf_token';

    public static function token(Session $session): string
    {
        $token = $session->get(self::TOKEN_KEY);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            $session->set(self::TOKEN_KEY, $token);
        }

        return $token;
    }

    public static function validate(Session $session, ?string $token): bool
    {
        $stored = $session->get(self::TOKEN_KEY);
        return is_string($token) && is_string($stored) && hash_equals($stored, $token);
    }
}
