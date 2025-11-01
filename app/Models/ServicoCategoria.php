<?php
declare(strict_types=1);

namespace App\Models;

class ServicoCategoria extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM servico_categorias ORDER BY nome');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO servico_categorias (nome, descricao, ativo) VALUES (:nome, :descricao, :ativo)');
        $stmt->execute([
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? null,
            'ativo' => (int) ($data['ativo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }
}
