<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\Usuario;

class AuthService
{
    private Usuario $usuarios;
    private Session $session;
    private const RATE_LIMIT_KEY = 'auth_attempts';

    public function __construct(private array $config)
    {
        $this->usuarios = new Usuario($config);
        $this->session = new Session();
    }

    public function attempt(string $email, string $senha): bool
    {
        $attempts = $this->session->get(self::RATE_LIMIT_KEY, ['count' => 0, 'timestamp' => time()]);
        if ($attempts['count'] >= 5 && time() - $attempts['timestamp'] < 300) {
            sleep(2);
            return false;
        }

        $usuario = $this->usuarios->findByEmail($email);
        if (!$usuario || !(bool) $usuario['ativo'] || !password_verify($senha, $usuario['senha_hash'])) {
            $attempts['count'] = ($attempts['count'] ?? 0) + 1;
            $attempts['timestamp'] = time();
            $this->session->set(self::RATE_LIMIT_KEY, $attempts);
            return false;
        }

        $this->session->set(self::RATE_LIMIT_KEY, ['count' => 0, 'timestamp' => time()]);
        $this->session->regenerate();
        $this->session->set('user', [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'papel' => $usuario['papel'],
        ]);

        $this->usuarios->recordLogin((int) $usuario['id']);
        return true;
    }

    public function logout(): void
    {
        $this->session->remove('user');
        session_destroy();
    }

    public function user(): ?array
    {
        return $this->session->get('user');
    }
}
