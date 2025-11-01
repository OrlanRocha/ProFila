<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use PDO;

class Usuario extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, nome, email, papel, ativo, ultimo_login, criado_em FROM usuarios ORDER BY nome');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nome, email, papel, ativo FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO usuarios (nome, email, senha_hash, papel, ativo) VALUES (:nome, :email, :senha_hash, :papel, :ativo)');
        $stmt->execute([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha_hash' => $data['senha_hash'],
            'papel' => $data['papel'],
            'ativo' => (int) $data['ativo'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET nome = :nome, email = :email, papel = :papel, ativo = :ativo WHERE id = :id');
        $stmt->execute([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'papel' => $data['papel'],
            'ativo' => (int) $data['ativo'],
            'id' => $id,
        ]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET senha_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => $hash, 'id' => $id]);
    }

    public function recordLogin(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET ultimo_login = :ultimo WHERE id = :id');
        $stmt->execute([
            'ultimo' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
