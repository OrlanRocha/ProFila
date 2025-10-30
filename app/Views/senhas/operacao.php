<div class="row g-4">
    <div class="col-lg-5">
        <div class="card bg-dark border-0 shadow-sm h-100">
            <div class="card-body">
                <h1 class="h4 text-white mb-3">Operação de guichê</h1>
                <form method="post" action="<?= htmlspecialchars($url('senhas/proxima'), ENT_QUOTES) ?>" id="formProxima" class="mb-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="guiche_id" id="guicheAtual">
                    <div class="mb-3">
                        <label class="form-label" for="guicheSelect">Guichê</label>
                        <select class="form-select bg-dark text-white border-secondary" id="guicheSelect" required>
                            <?php foreach ($guiches as $guiche): ?>
                                <option value="<?= (int) $guiche['id'] ?>" data-modo="<?= htmlspecialchars($guiche['modo_atendimento']) ?>" data-prioridades='<?= htmlspecialchars($guiche['prioridades_config']) ?>'><?= htmlspecialchars($guiche['numero'] . ' - ' . ($guiche['apelido'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="filaSelect">Fila (opcional)</label>
                        <select class="form-select bg-dark text-white border-secondary" name="fila_id" id="filaSelect">
                            <option value="">Automática</option>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= (int) $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="alert alert-secondary small" id="guicheInfo"></div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success"><i class="bi bi-megaphone me-2"></i>Chamar próxima senha</button>
                    </div>
                </form>
                <form method="post" action="<?= htmlspecialchars($url('senhas/rechamar'), ENT_QUOTES) ?>" id="formRechamar" class="mb-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="guiche_id" id="guicheRechamar">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-arrow-counterclockwise me-2"></i>Rechamar última</button>
                    </div>
                </form>
                <form method="post" action="<?= htmlspecialchars($url('senhas/finalizar'), ENT_QUOTES) ?>" id="formFinalizar" class="mb-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="input-group">
                        <input type="number" class="form-control bg-dark text-white border-secondary" name="id" placeholder="ID da senha" required>
                        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-check2-all me-2"></i>Finalizar</button>
                    </div>
                </form>
                <form method="post" action="<?= htmlspecialchars($url('senhas/transferir'), ENT_QUOTES) ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="row g-2 align-items-center">
                        <div class="col-5">
                            <input type="number" class="form-control bg-dark text-white border-secondary" name="id" placeholder="ID" required>
                        </div>
                        <div class="col-5">
                            <select class="form-select bg-dark text-white border-secondary" name="fila_destino" required>
                                <option value="">Fila destino</option>
                                <?php foreach ($filas as $fila): ?>
                                    <option value="<?= (int) $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-2 d-grid">
                            <button class="btn btn-outline-warning" type="submit"><i class="bi bi-arrow-left-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card bg-dark border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5 text-white mb-3">Últimas chamadas</h2>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Guichê</th>
                            <th>Fila</th>
                            <th>Horário</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($historico as $linha): ?>
                            <tr>
                                <td class="fw-semibold text-info"><?= htmlspecialchars($linha['codigo']) ?></td>
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
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('guicheSelect');
        const info = document.getElementById('guicheInfo');
        const inputAtual = document.getElementById('guicheAtual');
        const inputRechamar = document.getElementById('guicheRechamar');

        const atualizarInfo = () => {
            const option = select.selectedOptions[0];
            if (!option) { return; }
            inputAtual.value = option.value;
            inputRechamar.value = option.value;
            const modo = option.dataset.modo?.toUpperCase();
            let prioridades = [];
            try {
                prioridades = JSON.parse(option.dataset.prioridades || '[]');
            } catch (error) {
                prioridades = [];
            }
            info.innerHTML = `<div><strong>Modo:</strong> ${modo}</div><div><strong>Prioridades:</strong> ${prioridades.join(', ') || 'Padrão'}</div>`;
        };

        select.addEventListener('change', atualizarInfo);
        atualizarInfo();
    });
</script>
