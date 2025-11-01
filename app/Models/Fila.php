<?php
declare(strict_types=1);

namespace App\Models;

class Fila extends BaseModel
{
    public function all(array $filters = []): array
    {
        return $this->listWithFilters($filters);
    }

    public function listWithFilters(array $filters = []): array
    {
        $sql = "SELECT f.*, u.nome AS unidade_nome, u.codigo AS unidade_codigo, u.uo_nivel_i_id, u.uo_nivel_ii_id, u.uo_nivel_iii_id, "
             . "uo1.nome AS uo_nivel_i_nome, uo2.nome AS uo_nivel_ii_nome, uo3.nome AS uo_nivel_iii_nome "
             . "FROM filas f "
             . "LEFT JOIN unidades u ON u.id = f.unidade_id "
             . "LEFT JOIN uo_entidades uo1 ON uo1.id = u.uo_nivel_i_id "
             . "LEFT JOIN uo_entidades uo2 ON uo2.id = u.uo_nivel_ii_id "
             . "LEFT JOIN uo_entidades uo3 ON uo3.id = u.uo_nivel_iii_id "
             . "WHERE 1 = 1";
        $params = [];

        if (!empty($filters['uo_i'])) {
            $sql .= " AND u.uo_nivel_i_id = :uo_i";
            $params['uo_i'] = (int) $filters['uo_i'];
        }
        if (!empty($filters['uo_ii'])) {
            $sql .= " AND u.uo_nivel_ii_id = :uo_ii";
            $params['uo_ii'] = (int) $filters['uo_ii'];
        }
        if (!empty($filters['uo_iii'])) {
            $sql .= " AND u.uo_nivel_iii_id = :uo_iii";
            $params['uo_iii'] = (int) $filters['uo_iii'];
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'todas') {
            if ($filters['status'] === 'ativas') {
                $sql .= " AND f.ativo = 1";
            } elseif ($filters['status'] === 'inativas') {
                $sql .= " AND f.ativo = 0";
            }
        }

        $sql .= " ORDER BY COALESCE(u.nome, f.nome), f.nome";

        if ($params) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $this->db->query($sql);
        }

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
