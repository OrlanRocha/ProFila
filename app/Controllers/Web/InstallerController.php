<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Services\InstallerService;
use App\Core\Request;

class InstallerController extends BaseController
{
    public function __construct(private readonly InstallerService $installer)
    {
    }

    public function show(): \App\Core\Response
    {
        if ($this->installer->isInstalled()) {
            header('Location: /logout');
            exit;
        }

        return $this->view('install/index', [
            'title' => 'Instalação',
        ], 'auth');
    }

    public function step(Request $request): \App\Core\Response
    {
        if ($this->installer->isInstalled()) {
            return $this->json(['ok' => false, 'msg' => 'Já instalado'], 400);
        }

        $step = $request->input('step');
        $config = $request->all();
        $ok = false;
        switch ($step) {
            case 'env':
                $ok = $this->installer->createEnvIfMissing([
                    'APP_ENV' => $config['app_env'] ?? 'local',
                    'APP_URL' => $config['app_url'] ?? 'http://localhost/profila',
                    'APP_KEY' => 'base64:changeme',
                    'DB_HOST' => $config['db_host'] ?? '127.0.0.1',
                    'DB_PORT' => $config['db_port'] ?? '3306',
                    'DB_NAME' => $config['db_name'] ?? 'profila',
                    'DB_USER' => $config['db_user'] ?? 'root',
                    'DB_PASS' => $config['db_pass'] ?? 'secret',
                    'WS_HOST' => $config['ws_host'] ?? '127.0.0.1',
                    'WS_PORT' => $config['ws_port'] ?? '8080',
                ]);
                break;
            case 'migrate':
                $ok = $this->installer->migrate();
                break;
            case 'seed':
                $ok = $this->installer->seed();
                break;
            case 'finish':
                $ok = $this->installer->finalize();
                break;
            default:
                return $this->json(['ok' => false, 'msg' => 'Etapa inválida'], 400);
        }

        return $ok
            ? $this->json(['ok' => true])
            : $this->json(['ok' => false, 'msg' => 'Falha na etapa ' . $step], 400);
    }
}
