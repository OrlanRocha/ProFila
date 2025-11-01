<?php
/** @var array $filas */
/** @var array $unidades */
/** @var array $uoI */
/** @var array $uoII */
/** @var array $uoIII */
/** @var array $filters */
/** @var callable $url */
/** @var string $token */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 text-white mb-1">Filas de atendimento</h1>
        <p class="text-secondary mb-0">Gerencie regras de prioridade por ponto de atendimento, unidade e nível de UO.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-light" href="<?= htmlspecialchars($url('painel/index'), ENT_QUOTES) ?>"><i class="bi bi-display"></i> Painéis</a>
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filaModal"><i class="bi bi-plus-circle me-2"></i>Nova fila</button>
    </div>
</div>
<div class="filter-toolbar">
    <form class="row g-3 align-items-end" method="get">
        <div class="col-md-3">
            <label class="form-label" for="filtroUoI">UO nível I</label>
            <select class="form-select bg-dark" id="filtroUoI" name="uo_i">
                <option value="">Todas</option>
                <?php foreach ($uoI as $uo): ?>
                    <option value="<?= (int) $uo['id'] ?>" <?= ($filters['uo_i'] ?? null) === (int) $uo['id'] ? 'selected' : '' ?>><?= htmlspecialchars($uo['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="filtroUoII">UO nível II</label>
            <select class="form-select bg-dark" id="filtroUoII" name="uo_ii">
                <option value="">Todas</option>
                <?php foreach ($uoII as $uo): ?>
                    <option value="<?= (int) $uo['id'] ?>" <?= ($filters['uo_ii'] ?? null) === (int) $uo['id'] ? 'selected' : '' ?>><?= htmlspecialchars($uo['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="filtroUoIII">UO nível III</label>
            <select class="form-select bg-dark" id="filtroUoIII" name="uo_iii">
                <option value="">Todas</option>
                <?php foreach ($uoIII as $uo): ?>
                    <option value="<?= (int) $uo['id'] ?>" <?= ($filters['uo_iii'] ?? null) === (int) $uo['id'] ? 'selected' : '' ?>><?= htmlspecialchars($uo['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="filtroStatus">Status</label>
            <select class="form-select bg-dark" id="filtroStatus" name="status">
                <option value="todas" <?= ($filters['status'] ?? 'todas') === 'todas' ? 'selected' : '' ?>>Todas</option>
                <option value="ativas" <?= ($filters['status'] ?? 'todas') === 'ativas' ? 'selected' : '' ?>>Ativas</option>
                <option value="inativas" <?= ($filters['status'] ?? 'todas') === 'inativas' ? 'selected' : '' ?>>Inativas</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Pesquisar</button>
            <a class="btn btn-outline-light" href="<?= htmlspecialchars($url('filas/index'), ENT_QUOTES) ?>">Limpar</a>
        </div>
    </form>
</div>
<div class="card bg-dark border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle" id="filasTable">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Sigla</th>
                    <th>UO I</th>
                    <th>UO II</th>
                    <th>UO III</th>
                    <th>Unidade</th>
                    <th>Prioridade padrão</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($filas as $fila): ?>
                    <tr data-fila='<?= json_encode($fila, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>' data-uo-i='<?= (int) ($fila['uo_nivel_i_id'] ?? 0) ?>' data-uo-ii='<?= (int) ($fila['uo_nivel_ii_id'] ?? 0) ?>' data-uo-iii='<?= (int) ($fila['uo_nivel_iii_id'] ?? 0) ?>' data-status='<?= (int) $fila['ativo'] === 1 ? 'ativo' : 'inativo' ?>'>
                        <td class="fw-semibold text-white"><?= htmlspecialchars($fila['nome']) ?></td>
                        <td><span class="badge bg-info bg-opacity-25 text-info"><?= htmlspecialchars($fila['sigla']) ?></span></td>
                        <td><?= htmlspecialchars($fila['uo_nivel_i_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($fila['uo_nivel_ii_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($fila['uo_nivel_iii_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($fila['unidade_nome'] ?? '—') ?></td>
                        <td><?= (int) $fila['prioridade_padrao'] ?></td>
                        <td><?= $fila['ativo'] ? '<span class="badge text-bg-success">Ativa</span>' : '<span class="badge text-bg-secondary">Inativa</span>' ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-light me-2" data-action="edit"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="<?= (int) $fila['id'] ?>"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="filaModal" tabindex="-1" aria-labelledby="filaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content bg-dark text-white" method="post" action="<?= htmlspecialchars($url('filas/save'), ENT_QUOTES) ?>">
            <div class="modal-header border-secondary">
                <h2 class="modal-title fs-5" id="filaModalLabel">Fila</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="id" id="filaId">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label" for="filaNome">Nome</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" name="nome" id="filaNome" required>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label" for="filaSigla">Sigla</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" name="sigla" id="filaSigla" maxlength="5" required>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label" for="filaPrioridade">Prioridade padrão</label>
                        <input type="number" class="form-control bg-dark text-white border-secondary" name="prioridade_padrao" id="filaPrioridade" value="0" min="0" max="100">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="filaUnidade">Unidade organizacional</label>
                        <select class="form-select bg-dark text-white border-secondary" name="unidade_id" id="filaUnidade">
                            <option value="">Selecionar posteriormente</option>
                            <?php foreach ($unidades as $unidade): ?>
                                <option value="<?= (int) $unidade['id'] ?>"><?= htmlspecialchars($unidade['nome']) ?> — <?= htmlspecialchars($unidade['orgao_nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" id="filaAtivo" checked>
                            <label class="form-check-label" for="filaAtivo">Fila ativa</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-info">Salvar</button>
            </div>
        </form>
    </div>
</div>
<form id="filaDelete" method="post" class="d-none" action="<?= htmlspecialchars($url('filas/delete'), ENT_QUOTES) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="id" value="">
</form>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        $('#filasTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });

        const modal = document.getElementById('filaModal');
        modal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            if (button?.dataset.action === 'edit') {
                const data = JSON.parse(button.closest('tr').dataset.fila);
                document.getElementById('filaId').value = data.id;
                document.getElementById('filaNome').value = data.nome;
                document.getElementById('filaSigla').value = data.sigla;
                document.getElementById('filaPrioridade').value = data.prioridade_padrao;
                document.getElementById('filaUnidade').value = data.unidade_id || '';
                document.getElementById('filaAtivo').checked = parseInt(data.ativo, 10) === 1;
                document.getElementById('filaModalLabel').textContent = 'Editar fila';
            } else {
                modal.querySelector('form').reset();
                document.getElementById('filaId').value = '';
                document.getElementById('filaModalLabel').textContent = 'Nova fila';
            }
        });

        document.querySelectorAll('[data-action="edit"]').forEach(button => {
            button.addEventListener('click', () => {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                modalInstance.show(button);
            });
        });

        document.querySelectorAll('[data-action="delete"]').forEach(button => {
            button.addEventListener('click', () => {
                const form = document.getElementById('filaDelete');
                form.querySelector('input[name="id"]').value = button.dataset.id;
                Swal.fire({
                    title: 'Remover fila?',
                    text: 'Esta ação não pode ser desfeita e pode impactar guichês associados.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, remover',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
