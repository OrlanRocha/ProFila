<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use PDO;

class RelatorioService
{
    private PDO $db;

    public function __construct(private array $config)
    {
        $this->db = DB::connection($config);
    }

    public function indicadoresDiarios(): array
    {
        $sql = 'SELECT 
            SUM(CASE WHEN DATE(s.criado_em) = CURDATE() THEN 1 ELSE 0 END) AS emitidas_hoje,
            SUM(CASE WHEN s.status IN ("em_atendimento","finalizada") AND DATE(s.chamado_em) = CURDATE() THEN 1 ELSE 0 END) AS chamadas_hoje,
            AVG(TIMESTAMPDIFF(SECOND, s.chamado_em, a.fim)) AS tempo_medio
        FROM senhas s
        LEFT JOIN atendimentos a ON a.senha_id = s.id AND a.fim IS NOT NULL';
        $stmt = $this->db->query($sql);
        $data = $stmt->fetch() ?: [];
        $data['tempo_medio'] = $data['tempo_medio'] ? (int) $data['tempo_medio'] : 0;
        return $data;
    }

    public function volumePorFila(): array
    {
        $sql = 'SELECT f.nome, COUNT(*) AS total FROM senhas s INNER JOIN filas f ON f.id = s.fila_id WHERE DATE(s.criado_em) >= CURDATE() - INTERVAL 7 DAY GROUP BY f.nome ORDER BY total DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function volumePorDia(): array
    {
        $sql = 'SELECT DATE(s.criado_em) AS dia, COUNT(*) AS total FROM senhas s WHERE s.criado_em >= CURDATE() - INTERVAL 14 DAY GROUP BY dia ORDER BY dia ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function taxaPrioridade(): array
    {
        $sql = 'SELECT 
            SUM(CASE WHEN prioridade > 0 THEN 1 ELSE 0 END) AS prioritarias,
            COUNT(*) AS total
        FROM senhas WHERE criado_em >= CURDATE() - INTERVAL 7 DAY';
        $data = $this->db->query($sql)->fetch() ?: ['prioritarias' => 0, 'total' => 0];
        $data['taxa'] = ($data['total'] ?? 0) ? round(($data['prioritarias'] / $data['total']) * 100, 2) : 0;
        return $data;
    }
}
