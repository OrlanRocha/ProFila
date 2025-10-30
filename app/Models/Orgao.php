<?php
declare(strict_types=1);

namespace App\Models;

class Orgao extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM orgaos ORDER BY nome');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO orgaos (nome, sigla, ativo) VALUES (:nome, :sigla, :ativo)');
        $stmt->execute([
            'nome' => $data['nome'],
            'sigla' => $data['sigla'],
            'ativo' => (int) ($data['ativo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }
}
