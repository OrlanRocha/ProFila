<?php
declare(strict_types=1);

namespace App\Models;

class Uo extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM uo_entidades ORDER BY FIELD(nivel, "I","II","III"), nome');
        return $stmt->fetchAll();
    }

    public function porNivel(string $nivel): array
    {
        $stmt = $this->db->prepare('SELECT * FROM uo_entidades WHERE nivel = :nivel ORDER BY nome');
        $stmt->execute(['nivel' => $nivel]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO uo_entidades (nivel, nome, codigo, ativo) VALUES (:nivel, :nome, :codigo, :ativo)');
        $stmt->execute([
            'nivel' => $data['nivel'],
            'nome' => $data['nome'],
            'codigo' => $data['codigo'],
            'ativo' => (int) ($data['ativo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function allAtivas(): array
    {
        $stmt = $this->db->query('SELECT * FROM uo_entidades WHERE ativo = 1 ORDER BY nome');
        return $stmt->fetchAll();
    }
}
