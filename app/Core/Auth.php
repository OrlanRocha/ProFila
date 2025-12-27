<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Repositories\UserRepository;

class Auth
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);

        if (!$user instanceof User || !$user->verifyPassword($password)) {
            return false;
        }

        Session::set('user_id', $user->id);
        Session::set('user_role', $user->role);

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
    }
}
