<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\Atendimento;
use App\Models\Fila;
use App\Models\Guiche;
use App\Models\Senha;
use App\Models\LogEvento;
use DateTimeImmutable;
use PDO;
use Throwable;

class SenhaService
{
    private PDO $db;
    private Fila $filas;
    private Senha $senhas;
    private Atendimento $atendimentos;
    private Guiche $guiches;
    private LogEvento $logs;
    private PainelBroadcaster $broadcaster;

    public function __construct(private array $config)
    {
        $this->db = DB::connection($config);
        $this->filas = new Fila($config);
        $this->senhas = new Senha($config);
        $this->atendimentos = new Atendimento($config);
        $this->guiches = new Guiche($config);
        $this->logs = new LogEvento($config);
        $this->broadcaster = new PainelBroadcaster($config);
    }

    public function emitir(int $filaId, string $tipoPrioridade = 'padrao'): array
    {
        $fila = $this->filas->find($filaId);
        if (!$fila) {
            throw new \InvalidArgumentException('Fila não encontrada.');
        }

        $numero = $this->filas->nextSequencial($filaId);
        $codigo = sprintf('%s%04d', $fila['sigla'], $numero);
        $prioridadeValor = $this->calcularPrioridade($fila, $tipoPrioridade);
        $agora = new DateTimeImmutable();

        $this->db->beginTransaction();
        try {
            $senhaId = $this->senhas->emitir([
                'codigo' => $codigo,
                'fila_id' => $filaId,
                'unidade_id' => $fila['unidade_id'] ?? null,
                'prioridade' => $prioridadeValor,
                'prioridade_tipo' => $tipoPrioridade,
                'status' => 'aguardando',
                'guiche_id' => null,
                'criado_em' => $agora->format('Y-m-d H:i:s'),
            ]);

            $this->logs->registrar('senha_emitida', $senhaId, [
                'codigo' => $codigo,
                'fila' => $fila['nome'],
                'fila_id' => $filaId,
                'prioridade_tipo' => $tipoPrioridade,
                'prioridade_valor' => $prioridadeValor,
                'unidade_id' => $fila['unidade_id'] ?? null,
                'sequencial' => $numero,
                'emitida_em' => $agora->format(DATE_ATOM),
            ]);
            $this->db->commit();
        } catch (Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }

        return [
            'codigo' => $codigo,
            'fila' => $fila['nome'],
            'id' => $senhaId,
            'prioridade_tipo' => $tipoPrioridade,
        ];
    }

    public function chamarProxima(int $guicheId, ?int $filaId = null): ?array
    {
        $guiche = $this->guiches->find($guicheId);
        if (!$guiche || !(int) $guiche['ativo']) {
            throw new \InvalidArgumentException('Guichê inativo ou inexistente.');
        }

        $tiposPermitidos = $this->normalizarPrioridades($guiche['prioridades_config'] ?? null);
        $modo = $guiche['modo_atendimento'] ?? 'fifo';
        $unidadeId = $guiche['unidade_id'] ?? null;

        $this->db->beginTransaction();
        try {
            if ($filaId) {
                $senha = $this->senhas->proximaParaChamada($filaId, $tiposPermitidos, $modo);
            } elseif (!empty($guiche['fila_padrao_id'])) {
                $senha = $this->senhas->proximaParaChamada((int) $guiche['fila_padrao_id'], $tiposPermitidos, $modo);
            } else {
                $senha = $this->senhas->proximaGlobal($tiposPermitidos, $modo, $unidadeId ? (int) $unidadeId : null);
            }

            if (!$senha) {
                $this->db->rollBack();
                return null;
            }

            $this->senhas->registrarChamada((int) $senha['id'], $guicheId);
            $this->atendimentos->registrarInicio((int) $senha['id'], null);
            $fila = $this->filas->find((int) $senha['fila_id']);
            $esperaSegundos = max(0, time() - strtotime((string) $senha['criado_em']));
            $chamadaEm = new DateTimeImmutable();

            $this->logs->registrar('senha_chamada', (int) $senha['id'], [
                'codigo' => $senha['codigo'],
                'guiche' => $guiche['numero'],
                'guiche_id' => $guicheId,
                'fila' => $fila['nome'] ?? null,
                'fila_id' => (int) $senha['fila_id'],
                'unidade_id' => $fila['unidade_id'] ?? null,
                'prioridade_tipo' => $senha['prioridade_tipo'],
                'prioridade_valor' => (int) $senha['prioridade'],
                'espera_segundos' => $esperaSegundos,
                'modo_atendimento' => $modo,
                'chamada_em' => $chamadaEm->format(DATE_ATOM),
            ]);
            $this->db->commit();
        } catch (Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }

        $payload = [
            'senha_id' => (int) $senha['id'],
            'codigo' => $senha['codigo'],
            'guiche' => $guiche['numero'],
            'fila' => $fila['nome'] ?? '',
            'unidade_id' => $fila['unidade_id'] ?? null,
            'hora' => gmdate('c'),
            'prioridade' => (int) $senha['prioridade'],
            'prioridade_tipo' => $senha['prioridade_tipo'],
            'espera_segundos' => $esperaSegundos,
        ];

        $this->broadcaster->publishSenhaChamada($payload);
        return $payload;
    }

    public function rechamar(int $guicheId): ?array
    {
        $senha = $this->senhas->ultimaPorGuiche($guicheId);
        if (!$senha) {
            return null;
        }

        $fila = $this->filas->find((int) $senha['fila_id']);
        $guiche = $this->guiches->find($guicheId);
        $payload = [
            'senha_id' => (int) $senha['id'],
            'codigo' => $senha['codigo'],
            'guiche' => $guiche['numero'] ?? $guicheId,
            'fila' => $fila['nome'] ?? '',
            'unidade_id' => $fila['unidade_id'] ?? null,
            'hora' => gmdate('c'),
            'prioridade' => (int) $senha['prioridade'],
            'prioridade_tipo' => $senha['prioridade_tipo'],
            'contexto' => 'rechamada',
        ];
        $this->logs->registrar('senha_rechamada', (int) $senha['id'], $payload);
        $this->broadcaster->publishSenhaChamada($payload);
        return $payload;
    }

    public function finalizar(int $senhaId): void
    {
        $senha = $this->senhas->find($senhaId);
        if (!$senha) {
            throw new \InvalidArgumentException('Senha não encontrada.');
        }

        $this->senhas->atualizarStatus($senhaId, 'finalizada');
        $this->atendimentos->registrarFim($senhaId);

        $duracao = $senha['chamado_em'] ? max(0, time() - strtotime((string) $senha['chamado_em'])) : null;
        $this->logs->registrar('senha_finalizada', $senhaId, [
            'codigo' => $senha['codigo'],
            'fila' => $senha['fila_nome'] ?? null,
            'fila_id' => (int) $senha['fila_id'],
            'unidade_id' => $senha['unidade_id'],
            'guiche' => $senha['guiche_numero'] ?? $senha['guiche_id'],
            'guiche_id' => $senha['guiche_id'],
            'duracao_segundos' => $duracao,
            'status_anterior' => $senha['status'],
            'prioridade_tipo' => $senha['prioridade_tipo'],
            'finalizada_em' => gmdate('c'),
        ]);
    }

    public function transferir(int $senhaId, int $filaDestino): void
    {
        $fila = $this->filas->find($filaDestino);
        if (!$fila) {
            throw new \InvalidArgumentException('Fila destino inválida.');
        }

        $senha = $this->senhas->find($senhaId);
        if (!$senha) {
            throw new \InvalidArgumentException('Senha não encontrada para transferência.');
        }

        $this->senhas->transferir($senhaId, $filaDestino);
        $this->logs->registrar('senha_transferida', $senhaId, [
            'codigo' => $senha['codigo'],
            'fila_origem_id' => (int) $senha['fila_id'],
            'fila_origem' => $senha['fila_nome'] ?? null,
            'fila_destino_id' => $filaDestino,
            'fila_destino' => $fila['nome'],
            'unidade_destino_id' => $fila['unidade_id'] ?? null,
            'prioridade_tipo' => $senha['prioridade_tipo'],
            'transferida_em' => gmdate('c'),
        ]);
    }

    public function ultimaChamadaPainel(?int $unidadeId = null): ?array
    {
        $ultima = $this->broadcaster->getUltimaChamada($unidadeId);
        if ($ultima) {
            return $ultima;
        }

        $registro = $this->senhas->ultimaChamada($unidadeId);
        if ($registro) {
            return [
                'codigo' => $registro['codigo'],
                'guiche' => $registro['guiche_numero'] ?? $registro['guiche_id'],
                'fila' => $registro['fila_nome'] ?? '',
                'hora' => $registro['chamado_em'] ?? gmdate('c'),
                'unidade_id' => $unidadeId,
            ];
        }

        return null;
    }

    public function historicoPainel(?int $unidadeId = null): array
    {
        return $this->senhas->historicoRecentes(4, $unidadeId);
    }

    private function calcularPrioridade(array $fila, string $tipo): int
    {
        $base = (int) ($fila['prioridade_padrao'] ?? 0);
        return match ($tipo) {
            'preferencial' => max($base + 5, 10),
            '80+' => max($base + 8, 12),
            'servico' => max($base + 3, 5),
            default => $base,
        };
    }

    private function normalizarPrioridades(null|string|array $config): array
    {
        if (is_string($config)) {
            try {
                $decoded = json_decode($config, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $decoded = null;
            }
            $config = $decoded;
        }

        if (!is_array($config) || empty($config)) {
            return ['padrao'];
        }

        $permitidos = [];
        foreach ($config as $item) {
            if (is_string($item)) {
                $permitidos[] = $item;
            }
        }

        return $permitidos ?: ['padrao'];
    }
}
