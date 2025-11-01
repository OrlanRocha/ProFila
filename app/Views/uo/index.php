<?php
/** @var array $uoI */
/** @var array $uoII */
/** @var array $uoIII */
/** @var callable $url */
/** @var string $token */
?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-semibold text-white">Organizações &amp; Unidades</h1>
        <p class="text-secondary mb-0">Gerencie as entidades de nível I, II e III separadamente para segmentar filas, painéis e agendamentos.</p>
    </div>
    <button class="btn btn-info mt-3 mt-lg-0" data-bs-toggle="modal" data-bs-target="#novaUoModal">
        <i class="bi bi-plus-lg me-2"></i>Cadastrar UO
    </button>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-lg h-100 glass-card">
            <div class="card-body">
                <h2 class="h5 text-white">UO Nível I</h2>
                <p class="text-secondary small">Representam órgãos centrais e gestores.</p>
                <ul class="list-group list-group-flush mt-3">
                    <?php foreach ($uoI as $uo): ?>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center">
                            <span>
                                <strong><?= htmlspecialchars($uo['nome']) ?></strong><br>
                                <small class="text-secondary">Código: <?= htmlspecialchars($uo['codigo']) ?></small>
                            </span>
                            <span class="badge bg-info-subtle text-info">Ativa</span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($uoI)): ?>
                        <li class="list-group-item bg-transparent text-secondary">Nenhuma UO cadastrada no nível I.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-lg h-100 glass-card">
            <div class="card-body">
                <h2 class="h5 text-white">UO Nível II</h2>
                <p class="text-secondary small">Estruturas intermediárias (coordenações / diretorias regionais).</p>
                <ul class="list-group list-group-flush mt-3">
                    <?php foreach ($uoII as $uo): ?>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center">
                            <span>
                                <strong><?= htmlspecialchars($uo['nome']) ?></strong><br>
                                <small class="text-secondary">Código: <?= htmlspecialchars($uo['codigo']) ?></small>
                            </span>
                            <span class="badge bg-info-subtle text-info">Ativa</span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($uoII)): ?>
                        <li class="list-group-item bg-transparent text-secondary">Nenhuma UO cadastrada no nível II.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-lg h-100 glass-card">
            <div class="card-body">
                <h2 class="h5 text-white">UO Nível III</h2>
                <p class="text-secondary small">Postos de atendimento e unidades físicas.</p>
                <ul class="list-group list-group-flush mt-3">
                    <?php foreach ($uoIII as $uo): ?>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center">
                            <span>
                                <strong><?= htmlspecialchars($uo['nome']) ?></strong><br>
                                <small class="text-secondary">Código: <?= htmlspecialchars($uo['codigo']) ?></small>
                            </span>
                            <span class="badge bg-info-subtle text-info">Ativa</span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($uoIII)): ?>
                        <li class="list-group-item bg-transparent text-secondary">Nenhuma UO cadastrada no nível III.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="novaUoModal" tabindex="-1" aria-labelledby="novaUoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0">
                <h1 class="modal-title fs-5" id="novaUoModalLabel">Cadastrar nova UO</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="post" action="<?= htmlspecialchars($url('uo/salvar'), ENT_QUOTES) ?>" autocomplete="off">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nivel" class="form-label">Nível</label>
                        <select class="form-select" id="nivel" name="nivel" required>
                            <option value="I">Nível I</option>
                            <option value="II">Nível II</option>
                            <option value="III">Nível III</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da UO</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" required>
                    </div>
                    <div class="form-text text-secondary">Utilize um código curto para identificar a UO em relatórios e painéis.</div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Salvar UO</button>
                </div>
            </form>
        </div>
    </div>
</div>
