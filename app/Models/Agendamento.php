<?php
declare(strict_types=1);

namespace App\Models;

class Agendamento extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT a.*, s.nome AS servico_nome, u.nome AS unidade_nome, c.nome AS cliente_nome, o.nome AS orgao_nome
                FROM agendamentos a
                LEFT JOIN servicos s ON s.id = a.servico_id
                LEFT JOIN unidades u ON u.id = a.unidade_id
                LEFT JOIN clientes c ON c.id = u.cliente_id
                LEFT JOIN orgaos o ON o.id = u.orgao_id
                ORDER BY a.data_agendada DESC, a.hora_agendada DESC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO agendamentos (nome, documento, contato, prioridade_tipo, categoria, servico_id, unidade_id, data_agendada, hora_agendada, observacoes, origem)
            VALUES (:nome, :documento, :contato, :prioridade, :categoria, :servico, :unidade, :data, :hora, :obs, :origem)');
        $stmt->execute([
            'nome' => $data['nome'],
            'documento' => $data['documento'] ?? null,
            'contato' => $data['contato'] ?? null,
            'prioridade' => $data['prioridade_tipo'] ?? 'padrao',
            'categoria' => $data['categoria'] ?? null,
            'servico' => $data['servico_id'] !== '' ? (int) $data['servico_id'] : null,
            'unidade' => $data['unidade_id'] !== '' ? (int) $data['unidade_id'] : null,
            'data' => $data['data_agendada'] ?? null,
            'hora' => $data['hora_agendada'] ?? null,
            'obs' => $data['observacoes'] ?? null,
            'origem' => $data['origem'] ?? 'interno',
        ]);
        return (int) $this->db->lastInsertId();
    }
}
