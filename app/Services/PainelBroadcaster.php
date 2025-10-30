<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\LogEvento;

class PainelBroadcaster
{
    private LogEvento $logs;
    private string $cacheFile;

    public function __construct(private array $config)
    {
        $this->logs = new LogEvento($config);
        $this->cacheFile = __DIR__ . '/../../storage/cache/painel_last.json';
    }

    public function publishSenhaChamada(array $dados): void
    {
        $payload = [
            'codigo' => $dados['codigo'],
            'guiche' => $dados['guiche'] ?? null,
            'fila' => $dados['fila'] ?? null,
            'hora' => $dados['hora'] ?? gmdate('c'),
            'prioridade' => $dados['prioridade'] ?? null,
            'espera_segundos' => $dados['espera_segundos'] ?? null,
            'contexto' => 'painel_broadcast',
        ];

        $this->logs->registrar('senha_chamada', $dados['senha_id'] ?? null, $payload);

        if (!is_dir(dirname($this->cacheFile))) {
            mkdir(dirname($this->cacheFile), 0775, true);
        }

        file_put_contents($this->cacheFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function getUltimaChamada(): ?array
    {
        if (!is_file($this->cacheFile)) {
            return null;
        }

        try {
            $data = json_decode((string) file_get_contents($this->cacheFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return null;
        }

        return is_array($data) ? $data : null;
    }
}
