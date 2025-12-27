<?php

declare(strict_types=1);

namespace App\Services;

class InstallerService
{
    private string $lockFile;
    private string $envFile;
    private string $envExample;

    public function __construct()
    {
        $this->lockFile = __DIR__ . '/../../storage/cache/installed.lock';
        $this->envFile = __DIR__ . '/../../.env';
        $this->envExample = __DIR__ . '/../../.env.example';
    }

    public function isInstalled(): bool
    {
        return is_file($this->lockFile);
    }

    public function createEnvIfMissing(array $config = []): bool
    {
        if (is_file($this->envFile)) {
            return true;
        }

        $defaults = [
            'APP_ENV' => 'local',
            'APP_URL' => 'http://localhost/profila',
            'APP_KEY' => 'base64:changeme',
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '3306',
            'DB_NAME' => 'profila',
            'DB_USER' => 'root',
            'DB_PASS' => 'secret',
            'WS_HOST' => '127.0.0.1',
            'WS_PORT' => '8080',
            'SESSION_NAME' => 'profila_sess',
            'CSRF_ENABLED' => '1',
            'LOG_LEVEL' => 'info',
        ];

        $data = array_merge($defaults, $config);

        $content = '';
        foreach ($data as $key => $value) {
            $content .= $key . '=' . $value . PHP_EOL;
        }

        return (bool) file_put_contents($this->envFile, $content);
    }

    public function migrate(): bool
    {
        // No-op placeholder for real migrations
        return true;
    }

    public function seed(): bool
    {
        // Seeds are PHP-based and may fail without DB; return true for now.
        return true;
    }

    public function finalize(): bool
    {
        if (!is_dir(dirname($this->lockFile))) {
            mkdir(dirname($this->lockFile), 0777, true);
        }
        return (bool) file_put_contents($this->lockFile, 'installed');
    }
}
