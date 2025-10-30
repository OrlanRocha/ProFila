<?php
declare(strict_types=1);

namespace App\Models;

class Permissao extends BaseModel
{
    public function todas(): array
    {
        $stmt = $this->db->query('SELECT * FROM permissoes ORDER BY nome');
        return $stmt->fetchAll();
    }

    public function porPapel(string $papel): array
    {
        $stmt = $this->db->prepare('SELECT p.*, COALESCE(pp.permitido, 0) AS permitido FROM permissoes p LEFT JOIN papel_permissoes pp ON pp.permissao_id = p.id AND pp.papel = :papel ORDER BY p.nome');
        $stmt->execute(['papel' => $papel]);
        return $stmt->fetchAll();
    }

    public function atualizarPapel(string $papel, array $permissoes): void
    {
        $this->db->beginTransaction();
        $delete = $this->db->prepare('DELETE FROM papel_permissoes WHERE papel = :papel');
        $delete->execute(['papel' => $papel]);

        $insert = $this->db->prepare('INSERT INTO papel_permissoes (papel, permissao_id, permitido) VALUES (:papel, :permissao, :permitido)');
        foreach ($permissoes as $permissaoId => $permitido) {
            $insert->execute([
                'papel' => $papel,
                'permissao' => (int) $permissaoId,
                'permitido' => (int) $permitido,
            ]);
        }
        $this->db->commit();
    }
}
