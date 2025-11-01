<?php
$token = $display['token'] ?? '';
$assigned = ($display['status'] ?? 'pendente') === 'ativo' && !empty($display['unidade_nome']);
$endpoint = rtrim($config['app']['base_url'] ?? '', '/') . '/sse/stream.php?display=' . urlencode($token);
$fallback = $url('api/painel/last', ['display' => $token]);
?>
<div class="painel-wrapper bg-dark text-white position-relative overflow-hidden">
    <?php if (!$assigned): ?>
        <div class="position-absolute top-0 start-0 end-0 bg-warning text-dark text-center py-2 fw-semibold">
            Este painel aguardando vinculação. IP detectado: <?= htmlspecialchars($ip) ?>.
            Atribua uma unidade e regra na central de gestão para iniciar as chamadas.
        </div>
    <?php endif; ?>
    <div class="painel-main text-center py-5 mt-5">
        <h1 class="display-1 fw-bold" id="painelCodigo"><?= htmlspecialchars($ultima['codigo'] ?? '--') ?></h1>
        <p class="lead mb-4">Guichê <span id="painelGuiche"><?= htmlspecialchars($ultima['guiche'] ?? '--') ?></span></p>
        <p class="fs-5 text-secondary">Fila: <span id="painelFila"><?= htmlspecialchars($ultima['fila'] ?? '--') ?></span></p>
        <?php if (!empty($display['unidade_nome'])): ?>
            <div class="badge bg-info bg-opacity-25 text-info px-4 py-2 rounded-pill">Unidade: <?= htmlspecialchars($display['unidade_nome']) ?></div>
        <?php endif; ?>
    </div>
    <div class="painel-historico position-absolute bottom-0 start-0 end-0 bg-black bg-opacity-50 py-3">
        <div class="container">
            <div class="d-flex justify-content-between text-uppercase small text-secondary">
                <span>Histórico recente</span>
                <span id="painelHora">--:--</span>
            </div>
            <div class="row mt-2" id="painelHistorico">
                <?php foreach (array_slice($historico, 0, 3) as $linha): ?>
                    <div class="col-md-4">
                        <div class="card bg-transparent border-secondary text-white text-center">
                            <div class="card-body p-3">
                                <div class="fw-bold fs-4"><?= htmlspecialchars($linha['codigo']) ?></div>
                                <div class="text-secondary">Guichê <?= htmlspecialchars($linha['guiche_numero'] ?? $linha['guiche_id']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.ProFila = window.ProFila || {};
    window.ProFila.painel = {
        sse: <?= $sse && $assigned ? 'true' : 'false' ?>,
        pollInterval: <?= (int) $pollInterval ?>,
        endpoint: '<?= htmlspecialchars($endpoint, ENT_QUOTES) ?>',
        fallback: '<?= htmlspecialchars($fallback, ENT_QUOTES) ?>',
        history: <?= json_encode(array_map(static function ($item) {
            return [
                'codigo' => $item['codigo'] ?? '--',
                'guiche' => $item['guiche_numero'] ?? $item['guiche_id'] ?? '--',
                'fila' => $item['fila_nome'] ?? '--',
            ];
        }, array_slice($historico, 0, 3)), JSON_UNESCAPED_UNICODE) ?>
    };
</script>
<script src="<?= htmlspecialchars(($config['app']['base_url'] ?? '') . '/public/assets/js/painel.js', ENT_QUOTES) ?>"></script>
<style>
    body { background: #050505; }
    nav, footer { display: none !important; }
    .painel-wrapper { min-height: calc(100vh - 0px); }
</style>
