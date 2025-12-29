<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Repositories\UserRepository;

class Auth
{
    private static array $attempts = [];

    public function __construct(private readonly UserRepository $users)
    {
    }

    public function attempt(string $email, string $password): bool
    {
        if ($this->isLockedInternal($email)) {
            return false;
        }

        $user = $this->users->findByEmail($email);

        if (!$user instanceof User || !$user->active || !$user->verifyPassword($password)) {
            $this->registerAttempt($email, false);
            return false;
        }

        Session::set('user_id', $user->id);
        Session::set('user_role', $user->role);
        Session::set('user_role_id', $user->roleId);
        Session::set('user_scopes', $user->scopes);
        Session::set('user_permissions', $user->scopes);
        Session::set('user_name', $user->name);
        Session::set('user_email', $user->email);
        $this->registerAttempt($email, true);

        return true;
    }

    public function user(): ?User
    {
        $id = Session::get('user_id');
        if (!$id) {
            return null;
        }

        return $this->users->findById((int) $id);
    }

    public function checkRole(array $roles): bool
    {
        $current = Session::get('user_role');
        return in_array($current, $roles, true);
    }

    public function logout(): void
    {
        Session::remove('user_id');
        Session::remove('user_role');
        Session::remove('user_scopes');
        Session::remove('user_role_id');
    }

    public function isLocked(string $email): bool
    {
        return $this->isLockedInternal($email);
    }

    private function registerAttempt(string $email, bool $success): void
    {
        $key = $this->attemptKey($email);
        $entry = self::$attempts[$key] ?? ['count' => 0, 'locked_until' => null];

        if ($success) {
            self::$attempts[$key] = ['count' => 0, 'locked_until' => null];
            return;
        }

        $entry['count']++;
        if ($entry['count'] >= 5) {
            $entry['locked_until'] = time() + 600;
        }

        self::$attempts[$key] = $entry;
    }

    private function isLockedInternal(string $email): bool
    {
        $key = $this->attemptKey($email);
        $entry = self::$attempts[$key] ?? null;
        if (!$entry) {
            return false;
        }

        if ($entry['locked_until'] && $entry['locked_until'] > time()) {
            return true;
        }

        if ($entry['locked_until'] && $entry['locked_until'] <= time()) {
            self::$attempts[$key] = ['count' => 0, 'locked_until' => null];
            return false;
        }

        return false;
    }

    private function attemptKey(string $email): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        return $email . '|' . $ip;
    }
}
