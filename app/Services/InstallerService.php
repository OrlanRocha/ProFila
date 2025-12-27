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

    public function createEnvIfMissing(): bool
    {
        if (is_file($this->envFile)) {
            return true;
        }

        if (!is_file($this->envExample)) {
            return false;
        }

        return copy($this->envExample, $this->envFile);
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
