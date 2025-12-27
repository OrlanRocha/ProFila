<?php

declare(strict_types=1);

namespace App\Models;

class Attendance
{
    public function __construct(
        public int $id,
        public string $tipo, // planilhado|presencial|recepcao
        public string $cidadao,
        public string $documento,
        public int $queueId,
        public ?int $pontoId = null,
        public ?string $prioridade = null,
        public ?string $senha = null,
        public ?string $orgao = null
    ) {
    }
}
