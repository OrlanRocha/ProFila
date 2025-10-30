<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h1 class="h4 mb-3">Emitir senha</h1>
                <p class="text-muted">Selecione a fila e escolha entre senha normal ou prioritária.</p>
                <form method="post" class="row g-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="col-12">
                        <label class="form-label" for="fila">Fila</label>
                        <select class="form-select" name="fila_id" id="fila" required>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= (int) $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?> (<?= htmlspecialchars($fila['sigla']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-3">
                        <button type="submit" name="tipo" value="normal" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-ticket-perforated me-2"></i>Normal
                        </button>
                        <button type="submit" name="tipo" value="prioridade" class="btn btn-warning flex-grow-1">
                            <i class="bi bi-lightning-charge me-2"></i>Prioritária
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Dicas rápidas</h2>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle me-2 text-success"></i>Use senhas prioritárias para gestantes, idosos, PCDs e demais casos previstos em lei.</li>
                    <li class="mb-2"><i class="bi bi-check-circle me-2 text-success"></i>O número sequencial é controlado automaticamente por fila.</li>
                    <li><i class="bi bi-check-circle me-2 text-success"></i>As emissões são registradas no log de auditoria.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
