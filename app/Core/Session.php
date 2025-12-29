<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(getenv('SESSION_NAME') ?: 'profila_sess');
            session_start();
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * @return array<string,string>
     */
    public static function pullFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return is_array($flash) ? $flash : [];
    }

    public static function csrfToken(): string
    {
        $token = $_SESSION['_csrf'] ?? null;
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            $_SESSION['_csrf'] = $token;
        }
        return $token;
    }

    public static function destroy(): void
    {
        session_destroy();
    }
}
