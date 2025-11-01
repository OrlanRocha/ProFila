<?php
declare(strict_types=1);

namespace App\Models;

class PainelDisplay extends BaseModel
{
    public function registrarPorIp(string $ip): array
    {
        $existente = $this->buscarPorIp($ip);
        if ($existente) {
            $this->atualizarPing((int) $existente['id']);
            return $existente;
        }

        $token = $this->gerarTokenFallback();
        $stmt = $this->db->prepare('INSERT INTO painel_displays (ip_address, token, status, ultimo_visto) VALUES (:ip, :token, "pendente", NOW())');
        $stmt->execute(['ip' => $ip, 'token' => $token]);
        return $this->buscarPorIp($ip) ?? [];
    }

    public function buscarPorToken(string $token): ?array
    {
        $stmt = $this->db->prepare('SELECT d.*, u.nome AS unidade_nome, pr.nome AS regra_nome FROM painel_displays d LEFT JOIN unidades u ON u.id = d.unidade_id LEFT JOIN painel_regras pr ON pr.id = d.regra_id WHERE d.token = :token');
        $stmt->execute(['token' => $token]);
        $display = $stmt->fetch();
        return $display ?: null;
    }

    public function buscarPorIp(string $ip): ?array
    {
        $stmt = $this->db->prepare('SELECT d.*, u.nome AS unidade_nome, pr.nome AS regra_nome FROM painel_displays d LEFT JOIN unidades u ON u.id = d.unidade_id LEFT JOIN painel_regras pr ON pr.id = d.regra_id WHERE d.ip_address = :ip');
        $stmt->execute(['ip' => $ip]);
        $display = $stmt->fetch();
        return $display ?: null;
    }

    public function atualizarConfiguracao(int $id, ?int $unidadeId, ?int $regraId, string $status, ?string $apelido): void
    {
        $stmt = $this->db->prepare('UPDATE painel_displays SET unidade_id = :unidade, regra_id = :regra, status = :status, apelido = :apelido WHERE id = :id');
        $stmt->execute([
            'unidade' => $unidadeId,
            'regra' => $regraId,
            'status' => $status,
            'apelido' => $apelido,
            'id' => $id,
        ]);
    }

    public function atualizarPing(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE painel_displays SET ultimo_visto = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function listarTodos(): array
    {
        $sql = 'SELECT d.*, u.nome AS unidade_nome, o.nome AS orgao_nome, c.nome AS cliente_nome, pr.nome AS regra_nome
                FROM painel_displays d
                LEFT JOIN unidades u ON u.id = d.unidade_id
                LEFT JOIN orgaos o ON o.id = u.orgao_id
                LEFT JOIN clientes c ON c.id = u.cliente_id
                LEFT JOIN painel_regras pr ON pr.id = d.regra_id
                ORDER BY d.status DESC, d.ip_address';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    private function gerarTokenFallback(): string
    {
        $bytes = random_bytes(16);
        $hex = bin2hex($bytes);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', [
            substr($hex, 0, 4),
            substr($hex, 4, 4),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 4),
            substr($hex, 24, 4),
            substr($hex, 28, 4),
        ]);
    }
}
