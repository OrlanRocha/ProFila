<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Permission;
use App\Models\Role;

class RoleRepository
{
    /** @var array<int, Role> */
    private array $roles = [];
    /** @var array<int, Permission> */
    private array $permissions = [];

    public function __construct()
    {
        $this->seedPermissions();
        $this->seedRoles();
    }

    /** @return list<Role> */
    public function allRoles(): array
    {
        return array_values($this->roles);
    }

    /** @return list<Permission> */
    public function allPermissions(): array
    {
        return array_values($this->permissions);
    }

    public function findRole(int $id): ?Role
    {
        return $this->roles[$id] ?? null;
    }

    /** @return list<string> */
    public function permissionsByRole(int $roleId): array
    {
        return $this->roles[$roleId]->permissions ?? [];
    }

    private function seedPermissions(): void
    {
        $keys = [
            'ticket.create', 'ticket.next', 'ticket.call', 'ticket.recall', 'ticket.start', 'ticket.finish', 'ticket.cancel', 'ticket.transfer', 'ticket.reinsert',
            'queue.manage', 'policy.manage', 'monitor.manage', 'point.manage', 'channel.manage',
            'user.view', 'user.manage', 'role.manage', 'audit.view', 'system.params', 'report.view',
        ];

        $id = 1;
        foreach ($keys as $key) {
            $this->permissions[$id] = new Permission($id, $key, ucfirst(str_replace('.', ' ', $key)));
            $id++;
        }
    }

    private function seedRoles(): void
    {
        $perms = fn(string ...$keys) => $keys;
        $this->roles = [
            1 => new Role(1, 'Admin Master', array_column($this->permissions, 'key')),
            2 => new Role(2, 'Gestor', $perms('report.view', 'queue.manage', 'policy.manage', 'monitor.manage', 'channel.manage', 'user.view')),
            3 => new Role(3, 'Supervisor', $perms('ticket.*', 'ticket.cancel', 'ticket.transfer', 'ticket.reinsert', 'report.view')),
            4 => new Role(4, 'Atendente', $perms('ticket.create', 'ticket.next', 'ticket.call', 'ticket.start', 'ticket.finish', 'ticket.recall')),
            5 => new Role(5, 'Auditor', $perms('audit.view', 'report.view')),
        ];
    }
}
