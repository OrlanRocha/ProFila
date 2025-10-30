<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use PDO;

class Senha extends BaseModel
{
    public function emitir(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO senhas (codigo, fila_id, prioridade, status, guiche_id, criado_em) VALUES (:codigo, :fila_id, :prioridade, :status, :guiche_id, :criado_em)');
        $stmt->execute([
            'codigo' => $data['codigo'],
            'fila_id' => $data['fila_id'],
            'prioridade' => $data['prioridade'],
            'status' => $data['status'],
            'guiche_id' => $data['guiche_id'],
            'criado_em' => $data['criado_em'] ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT s.*, f.nome AS fila_nome, f.sigla AS fila_sigla, g.numero AS guiche_numero, g.apelido AS guiche_apelido FROM senhas s LEFT JOIN filas f ON f.id = s.fila_id LEFT JOIN guiches g ON g.id = s.guiche_id WHERE s.id = :id');
        $stmt->execute(['id' => $id]);
        $senha = $stmt->fetch();

        return $senha ?: null;
    }

    public function atualizarStatus(int $id, string $status, ?int $guicheId = null): void
    {
        $stmt = $this->db->prepare('UPDATE senhas SET status = :status, guiche_id = :guiche_id, atualizado_em = NOW() WHERE id = :id');
        $stmt->execute([
            'status' => $status,
            'guiche_id' => $guicheId,
            'id' => $id,
        ]);
    }

    public function registrarChamada(int $id, int $guicheId): void
    {
        $stmt = $this->db->prepare('UPDATE senhas SET status = "chamada", guiche_id = :guiche_id, chamado_em = NOW(), atualizado_em = NOW() WHERE id = :id');
        $stmt->execute([
            'guiche_id' => $guicheId,
            'id' => $id,
        ]);
    }

    public function proximaParaChamada(int $filaId, int $prioridadeMinima = 0): ?array
    {
        $sql = 'SELECT * FROM senhas WHERE fila_id = :fila AND status = "aguardando" AND prioridade >= :prioridade ORDER BY prioridade DESC, criado_em ASC LIMIT 1 FOR UPDATE';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fila' => $filaId,
            'prioridade' => $prioridadeMinima,
        ]);
        $senha = $stmt->fetch();
        return $senha ?: null;
    }

    public function proximaGlobal(): ?array
    {
        $sql = 'SELECT * FROM senhas WHERE status = "aguardando" ORDER BY prioridade DESC, criado_em ASC LIMIT 1 FOR UPDATE';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $senha = $stmt->fetch();
        return $senha ?: null;
    }

    public function ultimaPorGuiche(int $guicheId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM senhas WHERE guiche_id = :guiche ORDER BY chamado_em DESC LIMIT 1');
        $stmt->execute(['guiche' => $guicheId]);
        $senha = $stmt->fetch();
        return $senha ?: null;
    }

    public function transferir(int $id, int $filaId): void
    {
        $stmt = $this->db->prepare('UPDATE senhas SET fila_id = :fila, atualizado_em = NOW() WHERE id = :id');
        $stmt->execute(['fila' => $filaId, 'id' => $id]);
    }

    public function historicoRecentes(int $limit = 4): array
    {
        $stmt = $this->db->prepare('SELECT s.codigo, s.chamado_em, s.guiche_id, g.numero AS guiche_numero, g.apelido, f.nome AS fila_nome FROM senhas s LEFT JOIN guiches g ON g.id = s.guiche_id LEFT JOIN filas f ON f.id = s.fila_id WHERE s.status IN ("chamada", "em_atendimento", "finalizada") ORDER BY s.chamado_em DESC LIMIT :limite');
        $stmt->bindValue('limite', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function ultimaChamada(): ?array
    {
        $stmt = $this->db->query('SELECT s.codigo, s.chamado_em, s.guiche_id, g.numero AS guiche_numero, g.apelido, f.nome AS fila_nome FROM senhas s LEFT JOIN guiches g ON g.id = s.guiche_id LEFT JOIN filas f ON f.id = s.fila_id WHERE s.chamado_em IS NOT NULL ORDER BY s.chamado_em DESC LIMIT 1');
        $senha = $stmt->fetch();
        return $senha ?: null;
    }
}
