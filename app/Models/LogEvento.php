<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Session;
use DateTimeImmutable;

class LogEvento extends BaseModel
{
    private Session $session;
    private static ?string $requestId = null;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->session = new Session();

        if (self::$requestId === null) {
            try {
                self::$requestId = bin2hex(random_bytes(8));
            } catch (\Throwable) {
                self::$requestId = uniqid('req_', true);
            }
        }
    }

    public function registrar(string $tipo, ?int $referenciaId = null, array $payload = []): void
    {
        $contextualizado = $this->enriquecerPayload($tipo, $referenciaId, $payload);

        $stmt = $this->db->prepare('INSERT INTO logs_eventos (tipo, referencia_id, payload) VALUES (:tipo, :referencia, :payload)');
        $stmt->execute([
            'tipo' => $tipo,
            'referencia' => $referenciaId,
            'payload' => $contextualizado ? json_encode($contextualizado, JSON_THROW_ON_ERROR) : null,
        ]);
    }

    public function ultimos(int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT * FROM logs_eventos ORDER BY criado_em DESC LIMIT :limite');
        $stmt->bindValue('limite', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function enriquecerPayload(string $tipo, ?int $referenciaId, array $payload): array
    {
        $agora = new DateTimeImmutable();
        $usuario = $this->session->get('user');

        $meta = [
            'request_id' => self::$requestId,
            'session_id' => session_id() ?: null,
            'rota' => $_GET['r'] ?? ($_SERVER['REQUEST_URI'] ?? null),
            'metodo' => $_SERVER['REQUEST_METHOD'] ?? (PHP_SAPI === 'cli' ? 'CLI' : null),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 255) : null,
            'host' => function_exists('gethostname') ? gethostname() : null,
            'registrado_em' => $agora->format(DATE_ATOM),
            'referencia' => $referenciaId,
        ];

        if ($usuario) {
            $meta['ator'] = [
                'id' => $usuario['id'] ?? null,
                'nome' => $usuario['nome'] ?? null,
                'papel' => $usuario['papel'] ?? null,
            ];
        }

        $metaFiltrado = array_filter($meta, static fn($valor) => $valor !== null && $valor !== '');

        if (empty($payload)) {
            return ['meta' => $metaFiltrado, 'tipo' => $tipo];
        }

        return [
            'dados' => $payload,
            'meta' => $metaFiltrado,
            'tipo' => $tipo,
        ];
    }
}
