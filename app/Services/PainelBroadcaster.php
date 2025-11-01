<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\LogEvento;

class PainelBroadcaster
{
    private LogEvento $logs;
    private string $cacheDir;

    public function __construct(private array $config)
    {
        $this->logs = new LogEvento($config);
        $this->cacheDir = __DIR__ . '/../../storage/cache';
    }

    public function publishSenhaChamada(array $dados): void
    {
        $payload = [
            'codigo' => $dados['codigo'],
            'guiche' => $dados['guiche'] ?? null,
            'fila' => $dados['fila'] ?? null,
            'hora' => $dados['hora'] ?? gmdate('c'),
            'prioridade' => $dados['prioridade'] ?? null,
            'prioridade_tipo' => $dados['prioridade_tipo'] ?? null,
            'espera_segundos' => $dados['espera_segundos'] ?? null,
            'unidade_id' => $dados['unidade_id'] ?? null,
            'contexto' => 'painel_broadcast',
        ];

        $this->logs->registrar('senha_chamada', $dados['senha_id'] ?? null, $payload);

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }

        $arquivo = $this->arquivoCache($payload['unidade_id']);
        file_put_contents($arquivo, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function getUltimaChamada(?int $unidadeId = null): ?array
    {
        $arquivo = $this->arquivoCache($unidadeId);
        if (!is_file($arquivo)) {
            return null;
        }

        try {
            $data = json_decode((string) file_get_contents($arquivo), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($data) ? $data : null;
    }

    private function arquivoCache(?int $unidadeId): string
    {
        $suffix = $unidadeId ? '_unidade_' . $unidadeId : '_global';
        return rtrim($this->cacheDir, '/').'/painel_last' . $suffix . '.json';
    }
}
