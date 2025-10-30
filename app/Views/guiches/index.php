<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Guichês</h1>
        <p class="text-muted mb-0">Gerencie guichês e filas padrão.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#guicheModal"><i class="bi bi-plus-circle me-2"></i>Novo guichê</button>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="guichesTable">
                <thead>
                <tr>
                    <th>Número</th>
                    <th>Apelido</th>
                    <th>Fila padrão</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($guiches as $guiche): ?>
                    <tr data-guiche='<?= json_encode($guiche, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                        <td><?= (int) $guiche['numero'] ?></td>
                        <td><?= htmlspecialchars($guiche['apelido'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($guiche['fila_nome'] ?? '-') ?></td>
                        <td><?= $guiche['ativo'] ? '<span class="badge text-bg-success">Ativo</span>' : '<span class="badge text-bg-secondary">Inativo</span>' ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary me-2" data-action="edit"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="<?= (int) $guiche['id'] ?>"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="guicheModal" tabindex="-1" aria-labelledby="guicheModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="index.php?r=guiches/save">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="guicheModalLabel">Guichê</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="id" id="guicheId">
                <div class="mb-3">
                    <label class="form-label" for="guicheNumero">Número</label>
                    <input type="number" class="form-control" name="numero" id="guicheNumero" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="guicheApelido">Apelido</label>
                    <input type="text" class="form-control" name="apelido" id="guicheApelido">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="guicheFila">Fila padrão</label>
                    <select class="form-select" name="fila_padrao_id" id="guicheFila">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($filas as $fila): ?>
                            <option value="<?= (int) $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ativo" id="guicheAtivo" checked>
                    <label class="form-check-label" for="guicheAtivo">Guichê ativo</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
<form id="guicheDelete" method="post" class="d-none" action="index.php?r=guiches/delete">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="id" value="">
</form>
<script>
    window.ProFila = window.ProFila || {};
    window.ProFila.guicheToken = '<?= htmlspecialchars($token) ?>';
</script>
