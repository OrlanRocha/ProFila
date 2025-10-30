<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Dashboard</h1>
        <p class="text-muted mb-0">Indicadores em tempo real das operações.</p>
    </div>
</div>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-muted text-uppercase">Emitidas hoje</h2>
                <p class="display-6 fw-bold mb-0"><?= (int) ($indicadores['emitidas_hoje'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-muted text-uppercase">Chamadas hoje</h2>
                <p class="display-6 fw-bold mb-0"><?= (int) ($indicadores['chamadas_hoje'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-muted text-uppercase">Tempo médio (min)</h2>
                <p class="display-6 fw-bold mb-0"><?= round(((int) ($indicadores['tempo_medio'] ?? 0)) / 60, 1) ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h5 mb-3">Volume por fila (7 dias)</h2>
                <canvas id="graficoFila"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h5 mb-3">Volume por dia (14 dias)</h2>
                <canvas id="graficoDia"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h5 mb-3">Taxa de priorização (7 dias)</h2>
                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= (float) ($prioridade['taxa'] ?? 0) ?>">
                    <div class="progress-bar bg-warning" style="width: <?= (float) ($prioridade['taxa'] ?? 0) ?>%">
                        <?= (float) ($prioridade['taxa'] ?? 0) ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.ProFila = window.ProFila || {};
    window.ProFila.dashboard = {
        porFila: <?= json_encode($porFila, JSON_UNESCAPED_UNICODE) ?>,
        porDia: <?= json_encode($porDia, JSON_UNESCAPED_UNICODE) ?>
    };
</script>
