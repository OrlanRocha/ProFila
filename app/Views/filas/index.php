<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Filas</h1>
        <p class="text-muted mb-0">Configure filas, siglas e prioridades.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filaModal"><i class="bi bi-plus-circle me-2"></i>Nova fila</button>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="filasTable">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Sigla</th>
                    <th>Prioridade padrão</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($filas as $fila): ?>
                    <tr data-fila='<?= json_encode($fila, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                        <td><?= htmlspecialchars($fila['nome']) ?></td>
                        <td><?= htmlspecialchars($fila['sigla']) ?></td>
                        <td><?= (int) $fila['prioridade_padrao'] ?></td>
                        <td><?= $fila['ativo'] ? '<span class="badge text-bg-success">Ativa</span>' : '<span class="badge text-bg-secondary">Inativa</span>' ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary me-2" data-action="edit"><i class="bi bi-pencil"></i></button>
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
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="index.php?r=filas/save">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="filaModalLabel">Fila</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="id" id="filaId">
                <div class="mb-3">
                    <label class="form-label" for="filaNome">Nome</label>
                    <input type="text" class="form-control" name="nome" id="filaNome" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="filaSigla">Sigla</label>
                    <input type="text" class="form-control" name="sigla" id="filaSigla" maxlength="5" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="filaPrioridade">Prioridade padrão</label>
                    <input type="number" class="form-control" name="prioridade_padrao" id="filaPrioridade" value="0" min="0" max="100">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ativo" id="filaAtivo" checked>
                    <label class="form-check-label" for="filaAtivo">Fila ativa</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
<form id="filaDelete" method="post" class="d-none" action="index.php?r=filas/delete">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="id" value="">
</form>
<script>
    window.ProFila = window.ProFila || {};
    window.ProFila.filaToken = '<?= htmlspecialchars($token) ?>';
</script>
