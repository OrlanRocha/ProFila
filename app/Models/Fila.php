<?php
declare(strict_types=1);

namespace App\Models;

class Fila extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT f.*, u.nome AS unidade_nome FROM filas f LEFT JOIN unidades u ON u.id = f.unidade_id ORDER BY u.nome, f.nome');
        return $stmt->fetchAll();
    }

    public function byUnidade(?int $unidadeId): array
    {
        if ($unidadeId === null) {
            return $this->all();
        }

        $stmt = $this->db->prepare('SELECT f.*, u.nome AS unidade_nome FROM filas f LEFT JOIN unidades u ON u.id = f.unidade_id WHERE f.unidade_id = :unidade ORDER BY f.nome');
        $stmt->execute(['unidade' => $unidadeId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT f.*, u.nome AS unidade_nome FROM filas f LEFT JOIN unidades u ON u.id = f.unidade_id WHERE f.id = :id');
        $stmt->execute(['id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO filas (unidade_id, nome, sigla, prioridade_padrao, ativo, sequencial_atual) VALUES (:unidade_id, :nome, :sigla, :prioridade, :ativo, :sequencial)');
        $stmt->execute([
            'unidade_id' => $data['unidade_id'] ?? null,
            'nome' => $data['nome'],
            'sigla' => $data['sigla'],
            'prioridade' => (int) $data['prioridade_padrao'],
            'ativo' => (int) $data['ativo'],
            'sequencial' => (int) ($data['sequencial_atual'] ?? 0),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE filas SET unidade_id = :unidade_id, nome = :nome, sigla = :sigla, prioridade_padrao = :prioridade, ativo = :ativo WHERE id = :id');
        $stmt->execute([
            'unidade_id' => $data['unidade_id'] ?? null,
            'nome' => $data['nome'],
            'sigla' => $data['sigla'],
            'prioridade' => (int) $data['prioridade_padrao'],
            'ativo' => (int) $data['ativo'],
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM filas WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function nextSequencial(int $filaId): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare('SELECT sequencial_atual FROM filas WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $filaId]);
        $current = (int) $stmt->fetchColumn();
        $next = $current + 1;

        $update = $this->db->prepare('UPDATE filas SET sequencial_atual = :seq WHERE id = :id');
        $update->execute(['seq' => $next, 'id' => $filaId]);
        $this->db->commit();

        return $next;
    }
}
