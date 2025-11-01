<?php
declare(strict_types=1);

namespace App\Models;

class Cliente extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM clientes ORDER BY nome');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO clientes (nome, documento, ativo) VALUES (:nome, :documento, :ativo)');
        $stmt->execute([
            'nome' => $data['nome'],
            'documento' => $data['documento'] ?? null,
            'ativo' => (int) ($data['ativo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }
}
