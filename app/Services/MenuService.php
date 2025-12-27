<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RoleRepository;

class MenuService
{
    public function __construct(private readonly RoleRepository $roles)
    {
    }

    public function buildMenu(?int $roleId): array
    {
        $permissions = $roleId ? $this->roles->permissionsByRole($roleId) : [];
        $can = fn(string $perm) => in_array($perm, $permissions, true) || in_array(str_replace('.*', '', $perm) . '.*', $permissions, true);

        $items = [];

        if ($can('ticket.next') || $can('ticket.create')) {
            $items[] = ['label' => 'Atendimento', 'href' => '/atendimento'];
        }
        if ($can('report.view')) {
            $items[] = ['label' => 'Dashboards', 'href' => '/dashboards'];
        }
        if ($can('queue.manage') || $can('policy.manage')) {
            $items[] = ['label' => 'Gerenciamento', 'href' => '/gerenciamento'];
        }
        if ($can('queue.manage') || $can('user.manage')) {
            $items[] = ['label' => 'Cadastros', 'href' => '/cadastro'];
        }
        if ($can('monitor.manage') || $can('channel.manage')) {
            $items[] = ['label' => 'Configuração', 'href' => '/config'];
        }
        if ($can('user.manage')) {
            $items[] = ['label' => 'Usuários', 'href' => '/usuarios'];
        }
        if ($can('audit.view')) {
            $items[] = ['label' => 'Auditoria', 'href' => '/auditoria'];
        }

        return $items;
    }
}
