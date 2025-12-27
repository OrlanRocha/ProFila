<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;
use App\Models\User;
use App\Repositories\RoleRepository;
use PDO;

class UserRepository
{
    /** @var array<int, User> */
    private static array $memoryStore = [];
    private static int $sequence = 1;
    private RoleRepository $roles;

    public function __construct(?RoleRepository $roles = null)
    {
        $this->roles = $roles ?? new RoleRepository();
    }

    /** @return list<User> */
    public function all(): array
    {
        $rows = $this->fetchRows();

        if (!$rows) {
            $this->seedDefault();
            $rows = $this->fetchRows();
        }

        return $rows;
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->all() as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }

        return null;
    }

    public function findById(int $id): ?User
    {
        foreach ($this->all() as $user) {
            if ($user->id === $id) {
                return $user;
            }
        }

        return null;
    }

    public function create(array $data): User
    {
        $id = self::$sequence++;
        $roleId = (int) ($data['role_id'] ?? 4);
        $role = $this->roles->findRole($roleId);
        $roleName = $role?->name ?? ($data['role'] ?? 'user');
        $user = new User(
            $id,
            (string) $data['name'],
            (string) $data['email'],
            $roleId,
            (string) $roleName,
            password_hash((string) $data['password'], PASSWORD_DEFAULT),
            $data['scopes'] ?? [],
            (bool) ($data['active'] ?? true),
            $data['cpf'] ?? null
        );

        self::$memoryStore[$id] = $user;
        return $user;
    }

    public function update(int $id, array $data): ?User
    {
        $user = $this->findById($id);
        if (!$user) {
            return null;
        }

        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'] ?? $user->email;
        if (isset($data['role_id'])) {
            $roleId = (int) $data['role_id'];
            $role = $this->roles->findRole($roleId);
            $user->roleId = $roleId;
            $user->role = $role?->name ?? $user->role;
        } elseif (isset($data['role'])) {
            $user->role = (string) $data['role'];
        }
        $user->scopes = $data['scopes'] ?? $user->scopes;
        $user->active = (bool) ($data['active'] ?? $user->active);
        $user->cpf = $data['cpf'] ?? $user->cpf;

        if (!empty($data['password'])) {
            $user->passwordHash = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }

        self::$memoryStore[$id] = $user;
        return $user;
    }

    public function deactivate(int $id): bool
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }

        $user->active = false;
        self::$memoryStore[$id] = $user;
        return true;
    }

    /** @return list<User> */
    private function fetchRows(): array
    {
        if (self::$memoryStore) {
            return array_values(self::$memoryStore);
        }

        try {
            $pdo = DB::connection();
            $stmt = $pdo->query('SELECT id, name, email, role_id, role, password_hash, scopes, active, cpf FROM users LIMIT 200');
            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (\Throwable) {
            $rows = [];
        }

        if (!$rows) {
            return array_values(self::$memoryStore);
        }

        foreach ($rows as $row) {
            $scopes = $row['scopes'] ? json_decode((string) $row['scopes'], true, 512, JSON_THROW_ON_ERROR) : [];
            $roleId = isset($row['role_id']) ? (int) $row['role_id'] : 4;
            $role = $this->roles->findRole($roleId);
            $user = new User(
                (int) $row['id'],
                (string) $row['name'],
                (string) $row['email'],
                $roleId,
                (string) ($role?->name ?? $row['role'] ?? 'user'),
                (string) $row['password_hash'],
                $scopes,
                (bool) ($row['active'] ?? true),
                $row['cpf'] ?? null
            );
            self::$memoryStore[$user->id] = $user;
            self::$sequence = max(self::$sequence, $user->id + 1);
        }

        return array_values(self::$memoryStore);
    }

    private function seedDefault(): void
    {
        if (self::$memoryStore) {
            return;
        }

        $this->create([
            'name' => 'Admin',
            'email' => 'admin@local',
            'password' => 'secret',
            'role_id' => 1,
            'scopes' => ['*'],
        ]);
    }
}
