<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h1 class="h4 mb-3">Operação de guichê</h1>
                <form method="post" action="index.php?r=senhas/proxima&guiche=" id="formProxima">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="guicheSelect">Guichê</label>
                        <select class="form-select" name="guiche" id="guicheSelect" required>
                            <?php foreach ($guiches as $guiche): ?>
                                <option value="<?= (int) $guiche['id'] ?>"><?= htmlspecialchars($guiche['numero'] . ' - ' . ($guiche['apelido'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="filaSelect">Fila (opcional)</label>
                        <select class="form-select" name="fila_id" id="filaSelect">
                            <option value="">Automático</option>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= (int) $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success"><i class="bi bi-megaphone me-2"></i>Chamar próxima</button>
                    </div>
                </form>
                <form method="post" action="index.php?r=senhas/rechamar&guiche=" id="formRechamar" class="mt-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-arrow-counterclockwise me-2"></i>Rechamar última</button>
                    </div>
                </form>
                <form method="post" action="index.php?r=senhas/finalizar" id="formFinalizar" class="mt-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="input-group">
                        <input type="number" class="form-control" name="id" placeholder="ID da senha" required>
                        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-check2-all me-2"></i>Finalizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Últimas chamadas</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Guichê</th>
                            <th>Fila</th>
                            <th>Hora</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($historico as $linha): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($linha['codigo']) ?></td>
                                <td><?= htmlspecialchars($linha['guiche_numero'] ?? $linha['guiche_id']) ?></td>
                                <td><?= htmlspecialchars($linha['fila_nome'] ?? '') ?></td>
                                <td><?= $linha['chamado_em'] ? date('H:i', strtotime($linha['chamado_em'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.ProFila = window.ProFila || {};
    window.ProFila.operacaoToken = '<?= htmlspecialchars($token) ?>';
</script>
