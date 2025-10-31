<?php
declare(strict_types=1);

namespace App\Models;

class Unidade extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT u.*, o.nome AS orgao_nome, c.nome AS cliente_nome,
                       uo1.nome AS uo_nivel_i_nome, uo2.nome AS uo_nivel_ii_nome, uo3.nome AS uo_nivel_iii_nome
                FROM unidades u
                INNER JOIN orgaos o ON o.id = u.orgao_id
                INNER JOIN clientes c ON c.id = u.cliente_id
                LEFT JOIN uo_entidades uo1 ON uo1.id = u.uo_nivel_i_id
                LEFT JOIN uo_entidades uo2 ON uo2.id = u.uo_nivel_ii_id
                LEFT JOIN uo_entidades uo3 ON uo3.id = u.uo_nivel_iii_id
                ORDER BY o.nome, u.nome';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO unidades (orgao_id, cliente_id, nome, codigo, ativo, uo_nivel_i_id, uo_nivel_ii_id, uo_nivel_iii_id)
            VALUES (:orgao_id, :cliente_id, :nome, :codigo, :ativo, :uo_i, :uo_ii, :uo_iii)');
        $stmt->execute([
            'orgao_id' => $data['orgao_id'],
            'cliente_id' => $data['cliente_id'],
            'nome' => $data['nome'],
            'codigo' => $data['codigo'],
            'ativo' => (int) ($data['ativo'] ?? 1),
            'uo_i' => $data['uo_nivel_i_id'] ?? null,
            'uo_ii' => $data['uo_nivel_ii_id'] ?? null,
            'uo_iii' => $data['uo_nivel_iii_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
