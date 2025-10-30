<?php
declare(strict_types=1);

namespace App\Models;

class PainelRegra extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM painel_regras WHERE ativo = 1 ORDER BY nome');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO painel_regras (nome, descricao, configuracao, ativo) VALUES (:nome, :descricao, CAST(:configuracao AS JSON), :ativo)');
        $stmt->execute([
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? null,
            'configuracao' => json_encode($data['configuracao'] ?? []),
            'ativo' => (int) ($data['ativo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }
}
