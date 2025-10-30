<?php
declare(strict_types=1);

use App\Services\SenhaService;
use App\Core\Session;

require_once __DIR__ . '/../bootstrap.php';

ignore_user_abort(true);
set_time_limit(0);

theader();
function theader(): void
{
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
}

$config = require __DIR__ . '/../config/env.php';
Session::start();
$service = new SenhaService($config);

$lastCodigo = '';
$iterations = 0;
while (!connection_aborted() && $iterations < 3600) {
    $dados = $service->ultimaChamadaPainel();
    if ($dados && ($dados['codigo'] ?? '') !== $lastCodigo) {
        $lastCodigo = $dados['codigo'];
        echo "event: senha\n";
        echo 'data: ' . json_encode([
            'codigo' => $dados['codigo'] ?? '--',
            'guiche' => $dados['guiche'] ?? ($dados['guiche_numero'] ?? ''),
            'fila' => $dados['fila'] ?? ($dados['fila_nome'] ?? ''),
            'hora' => date(DATE_ATOM),
        ]) . "\n\n";
        @ob_flush();
        flush();
    }
    $iterations++;
    sleep(1);
}
