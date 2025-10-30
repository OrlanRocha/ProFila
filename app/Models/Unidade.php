<?php
declare(strict_types=1);

namespace App\Models;

class Unidade extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT u.*, o.nome AS orgao_nome, c.nome AS cliente_nome FROM unidades u INNER JOIN orgaos o ON o.id = u.orgao_id INNER JOIN clientes c ON c.id = u.cliente_id ORDER BY o.nome, u.nome';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO unidades (orgao_id, cliente_id, nome, codigo, ativo) VALUES (:orgao_id, :cliente_id, :nome, :codigo, :ativo)');
        $stmt->execute([
            'orgao_id' => $data['orgao_id'],
            'cliente_id' => $data['cliente_id'],
            'nome' => $data['nome'],
            'codigo' => $data['codigo'],
            'ativo' => (int) ($data['ativo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }
}
