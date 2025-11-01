<?php
declare(strict_types=1);

namespace App\Models;

class Atendimento extends BaseModel
{
    public function registrarInicio(int $senhaId, ?int $atendenteId = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO atendimentos (senha_id, atendente_id, inicio) VALUES (:senha, :atendente, NOW())');
        $stmt->execute([
            'senha' => $senhaId,
            'atendente' => $atendenteId,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function registrarFim(int $senhaId): void
    {
        $stmt = $this->db->prepare('UPDATE atendimentos SET fim = NOW() WHERE senha_id = :senha AND fim IS NULL');
        $stmt->execute(['senha' => $senhaId]);
    }
}
