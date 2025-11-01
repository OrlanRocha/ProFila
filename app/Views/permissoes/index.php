<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-semibold text-white">Matriz de Permissões</h1>
        <p class="text-secondary mb-0">Ative ou revogue o acesso de cada perfil às funcionalidades do ProFila.</p>
    </div>
    <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2">Alterações têm efeito imediato</span>
</div>
<form method="post" action="<?= htmlspecialchars($url('permissoes/salvar'), ENT_QUOTES) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
        <table class="table table-dark table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th>Funcionalidade</th>
                    <th class="text-center">Admin</th>
                    <th class="text-center">Gestor</th>
                    <th class="text-center">Atendente</th>
                    <th class="text-center">Visor</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($permissoes['admin'] as $indice => $permissao): ?>
                <tr>
                    <td>
                        <div class="fw-semibold text-white"><?= htmlspecialchars($permissao['nome']) ?></div>
                        <div class="text-secondary small"><?= htmlspecialchars($permissao['descricao'] ?? '') ?></div>
                    </td>
                    <?php foreach (['admin','gestor','atendente','visor'] as $papel): ?>
                        <?php $registro = $permissoes[$papel][$indice]; ?>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" role="switch" name="permissoes[<?= htmlspecialchars($papel) ?>][<?= (int) $registro['id'] ?>]" value="1" <?= (int) $registro['permitido'] === 1 ? 'checked' : '' ?>>
                            </div>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="text-end mt-4">
        <button class="btn btn-info px-4">Salvar matriz</button>
    </div>
</form>
