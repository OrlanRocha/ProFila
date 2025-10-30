<div class="row g-4">
    <div class="col-lg-6">
        <div class="card bg-dark border-0 shadow-sm h-100">
            <div class="card-body">
                <h1 class="h4 text-white mb-3">Emitir senha</h1>
                <p class="text-secondary">Selecione a fila e escolha a categoria de atendimento. As prioridades são rastreadas no log de auditoria.</p>
                <form method="post" class="row g-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="col-12">
                        <label class="form-label" for="fila">Fila</label>
                        <select class="form-select bg-dark text-white border-secondary" name="fila_id" id="fila" required>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= (int) $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?> (<?= htmlspecialchars($fila['sigla']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-6">
                                <button type="submit" name="tipo" value="padrao" class="btn btn-outline-light w-100 py-3">
                                    <i class="bi bi-person me-2"></i>Atendimento padrão
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="tipo" value="preferencial" class="btn btn-warning w-100 py-3">
                                    <i class="bi bi-heart-pulse me-2"></i>Preferencial
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="tipo" value="80+" class="btn btn-danger w-100 py-3">
                                    <i class="bi bi-person-raised-hand me-2"></i>80+
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="tipo" value="servico" class="btn btn-info w-100 py-3">
                                    <i class="bi bi-briefcase me-2"></i>Serviço específico
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card bg-dark border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5 text-white mb-3">Orientações</h2>
                <ul class="list-unstyled small text-secondary mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Prioridade “Preferencial” atende gestantes, PCDs e demais grupos legais.</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Categoria “Serviço” permite filas específicas, como atendimentos agendados.</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Todas as emissões registram unidade, usuário e carimbo de tempo.</li>
                    <li><i class="bi bi-check-circle text-success me-2"></i>Use as categorias para alimentar relatórios de prioridade e SLA.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
