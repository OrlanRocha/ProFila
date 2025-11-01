<?php
declare(strict_types=1);

use App\Services\SenhaService;
use App\Core\Session;
use App\Models\PainelDisplay;

require_once __DIR__ . '/../bootstrap.php';

ignore_user_abort(true);
set_time_limit(0);

function theader(): void
{
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
}

theader();

$config = require __DIR__ . '/../config/env.php';
Session::start();
$service = new SenhaService($config);
$displayModel = new PainelDisplay($config);

$token = $_GET['display'] ?? '';
$display = $token ? $displayModel->buscarPorToken($token) : null;
$unidadeId = $display && !empty($display['unidade_id']) && $display['status'] === 'ativo' ? (int) $display['unidade_id'] : null;

$lastCodigo = '';
$iterations = 0;
while (!connection_aborted() && $iterations < 3600) {
    if ($display) {
        $displayModel->atualizarPing((int) $display['id']);
    }
    $dados = $service->ultimaChamadaPainel($unidadeId);
    if ($dados && ($dados['codigo'] ?? '') !== $lastCodigo) {
        $lastCodigo = $dados['codigo'];
        echo "event: senha\n";
        echo 'data: ' . json_encode([
            'codigo' => $dados['codigo'] ?? '--',
            'guiche' => $dados['guiche'] ?? ($dados['guiche_numero'] ?? ''),
            'fila' => $dados['fila'] ?? ($dados['fila_nome'] ?? ''),
            'hora' => date(DATE_ATOM),
            'unidade_id' => $unidadeId,
        ]) . "\n\n";
        @ob_flush();
        flush();
    }
    $iterations++;
    sleep(1);
}
