<?php
declare(strict_types=1);

namespace App\Models;

class Servico extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT s.*, c.nome AS categoria_nome FROM servicos s LEFT JOIN servico_categorias c ON c.id = s.categoria_id ORDER BY c.nome, s.nome';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function ativos(): array
    {
        $sql = 'SELECT s.*, c.nome AS categoria_nome FROM servicos s LEFT JOIN servico_categorias c ON c.id = s.categoria_id WHERE s.ativo = 1 ORDER BY c.nome, s.nome';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO servicos (categoria_id, nome, descricao, duracao_minutos, ativo) VALUES (:categoria, :nome, :descricao, :duracao, :ativo)');
        $stmt->execute([
            'categoria' => $data['categoria_id'] !== '' ? (int) $data['categoria_id'] : null,
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? null,
            'duracao' => (int) ($data['duracao_minutos'] ?? 0),
            'ativo' => (int) ($data['ativo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }
}
