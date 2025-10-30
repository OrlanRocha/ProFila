<?php
declare(strict_types=1);

namespace App\Models;

class Fila extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM filas ORDER BY nome');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM filas WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO filas (nome, sigla, prioridade_padrao, ativo, sequencial_atual) VALUES (:nome, :sigla, :prioridade, :ativo, :sequencial)');
        $stmt->execute([
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
        $stmt = $this->db->prepare('UPDATE filas SET nome = :nome, sigla = :sigla, prioridade_padrao = :prioridade, ativo = :ativo WHERE id = :id');
        $stmt->execute([
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
