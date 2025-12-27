<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use App\Core\DB;

$pdo = DB::connection();

$pdo->exec('CREATE TABLE IF NOT EXISTS roles (id INT PRIMARY KEY, name VARCHAR(100) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
$pdo->exec('CREATE TABLE IF NOT EXISTS permissions (id INT PRIMARY KEY, `key` VARCHAR(100) NOT NULL, description VARCHAR(255) NULL)');
$pdo->exec('CREATE TABLE IF NOT EXISTS role_permissions (role_id INT NOT NULL, permission_id INT NOT NULL)');

$pdo->exec('TRUNCATE TABLE role_permissions');
$pdo->exec('TRUNCATE TABLE permissions');
$pdo->exec('TRUNCATE TABLE roles');
$pdo->exec('TRUNCATE TABLE users');

$roles = [
    ['id' => 1, 'name' => 'Admin Master'],
    ['id' => 2, 'name' => 'Gestor'],
    ['id' => 3, 'name' => 'Supervisor'],
    ['id' => 4, 'name' => 'Atendente'],
    ['id' => 5, 'name' => 'Auditor'],
];

$permissions = [
    'ticket.create','ticket.next','ticket.call','ticket.recall','ticket.start','ticket.finish','ticket.cancel','ticket.transfer','ticket.reinsert',
    'queue.manage','policy.manage','monitor.manage','point.manage','channel.manage','user.view','user.manage','role.manage','audit.view','system.params','report.view'
];

$stmtRole = $pdo->prepare('INSERT INTO roles (id, name) VALUES (:id, :name)');
foreach ($roles as $role) {
    $stmtRole->execute($role);
}

$stmtPerm = $pdo->prepare('INSERT INTO permissions (id, `key`, description) VALUES (:id, :key, :description)');
foreach ($permissions as $idx => $perm) {
    $stmtPerm->execute(['id' => $idx + 1, 'key' => $perm, 'description' => $perm]);
}

$stmtRolePerm = $pdo->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (:role, :perm)');
foreach ($roles as $role) {
    foreach ($permissions as $idx => $perm) {
        if ($role['id'] === 1) {
            $stmtRolePerm->execute(['role' => $role['id'], 'perm' => $idx + 1]);
        } elseif ($role['id'] === 4 && str_starts_with($perm, 'ticket.')) {
            $stmtRolePerm->execute(['role' => $role['id'], 'perm' => $idx + 1]);
        } elseif ($role['id'] === 2 && in_array($perm, ['report.view','queue.manage','policy.manage','monitor.manage','channel.manage','user.view'], true)) {
            $stmtRolePerm->execute(['role' => $role['id'], 'perm' => $idx + 1]);
        }
    }
}

$hash = password_hash('secret', PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO users (name, email, cpf, role_id, role, password_hash, scopes, active) VALUES (:name, :email, :cpf, :role_id, :role, :hash, :scopes, 1)')
    ->execute([
        'name' => 'Admin',
        'email' => 'admin@local',
        'cpf' => '000.000.000-00',
        'role_id' => 1,
        'role' => 'Admin Master',
        'hash' => $hash,
        'scopes' => json_encode(['*'], JSON_THROW_ON_ERROR),
    ]);

$pdo->prepare('INSERT INTO users (name, email, cpf, role_id, role, password_hash, scopes, active) VALUES (:name, :email, :cpf, :role_id, :role, :hash, :scopes, 1)')
    ->execute([
        'name' => 'Developer',
        'email' => 'dev@local',
        'cpf' => '111.111.111-11',
        'role_id' => 2,
        'role' => 'Gestor',
        'hash' => password_hash('dev123', PASSWORD_DEFAULT),
        'scopes' => json_encode(['*'], JSON_THROW_ON_ERROR),
    ]);

echo "Seeds executados com sucesso.\n";
