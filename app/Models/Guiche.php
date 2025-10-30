<?php
declare(strict_types=1);

namespace App\Models;

class Guiche extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT g.*, f.nome AS fila_nome FROM guiches g LEFT JOIN filas f ON f.id = g.fila_padrao_id ORDER BY g.numero';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM guiches WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $guiche = $stmt->fetch();
        return $guiche ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO guiches (numero, apelido, fila_padrao_id, ativo) VALUES (:numero, :apelido, :fila_padrao_id, :ativo)');
        $stmt->execute([
            'numero' => (int) $data['numero'],
            'apelido' => $data['apelido'] ?: null,
            'fila_padrao_id' => $data['fila_padrao_id'] ?: null,
            'ativo' => (int) $data['ativo'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE guiches SET numero = :numero, apelido = :apelido, fila_padrao_id = :fila_padrao_id, ativo = :ativo WHERE id = :id');
        $stmt->execute([
            'numero' => (int) $data['numero'],
            'apelido' => $data['apelido'] ?: null,
            'fila_padrao_id' => $data['fila_padrao_id'] ?: null,
            'ativo' => (int) $data['ativo'],
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM guiches WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
