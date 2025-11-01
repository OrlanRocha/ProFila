<?php
declare(strict_types=1);

namespace App\Models;

class Guiche extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT g.*, f.nome AS fila_nome, u.nome AS unidade_nome FROM guiches g LEFT JOIN filas f ON f.id = g.fila_padrao_id LEFT JOIN unidades u ON u.id = g.unidade_id ORDER BY u.nome, g.numero';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT g.*, f.nome AS fila_nome, u.nome AS unidade_nome FROM guiches g LEFT JOIN filas f ON f.id = g.fila_padrao_id LEFT JOIN unidades u ON u.id = g.unidade_id WHERE g.id = :id');
        $stmt->execute(['id' => $id]);
        $guiche = $stmt->fetch();
        return $guiche ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO guiches (unidade_id, numero, apelido, fila_padrao_id, ativo, modo_atendimento, prioridades_config) VALUES (:unidade_id, :numero, :apelido, :fila_padrao_id, :ativo, :modo, CAST(:prioridades AS JSON))');
        $stmt->execute([
            'unidade_id' => $data['unidade_id'] ?? null,
            'numero' => (int) $data['numero'],
            'apelido' => $data['apelido'] ?: null,
            'fila_padrao_id' => $data['fila_padrao_id'] ?: null,
            'ativo' => (int) $data['ativo'],
            'modo' => $data['modo_atendimento'] ?? 'fifo',
            'prioridades' => json_encode($data['prioridades_config'] ?? ['padrao']),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE guiches SET unidade_id = :unidade_id, numero = :numero, apelido = :apelido, fila_padrao_id = :fila_padrao_id, ativo = :ativo, modo_atendimento = :modo, prioridades_config = CAST(:prioridades AS JSON) WHERE id = :id');
        $stmt->execute([
            'unidade_id' => $data['unidade_id'] ?? null,
            'numero' => (int) $data['numero'],
            'apelido' => $data['apelido'] ?: null,
            'fila_padrao_id' => $data['fila_padrao_id'] ?: null,
            'ativo' => (int) $data['ativo'],
            'modo' => $data['modo_atendimento'] ?? 'fifo',
            'prioridades' => json_encode($data['prioridades_config'] ?? ['padrao']),
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM guiches WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function usuarios(int $guicheId): array
    {
        $stmt = $this->db->prepare('SELECT gu.usuario_id, u.nome, gu.perfil FROM guiche_usuarios gu INNER JOIN usuarios u ON u.id = gu.usuario_id WHERE gu.guiche_id = :guiche');
        $stmt->execute(['guiche' => $guicheId]);
        return $stmt->fetchAll();
    }
}
