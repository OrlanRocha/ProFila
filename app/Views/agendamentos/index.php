<?php
/** @var array $agendamentos */
/** @var array $servicos */
/** @var array $unidades */
/** @var array $categorias */
/** @var array $uos */
/** @var callable $url */
/** @var string $token */
?>
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
        <h1 class="h3 fw-semibold text-white">Agendamentos inteligentes</h1>
        <p class="text-secondary mb-0">Visualize, filtre e crie reservas de atendimento alinhadas às prioridades e serviços disponíveis.</p>
    </div>
    <button class="btn btn-info mt-3 mt-lg-0" data-bs-toggle="offcanvas" data-bs-target="#novoAgendamento">
        <i class="bi bi-calendar-plus me-2"></i>Novo agendamento
    </button>
</div>
<?php if (!empty($uos)): ?>
    <div class="d-flex flex-wrap gap-3 mb-4">
        <?php foreach (['I' => 'Nível I', 'II' => 'Nível II', 'III' => 'Nível III'] as $nivel => $label): ?>
            <?php $total = array_reduce($uos, fn($carry, $uo) => $carry + ($uo['nivel'] === $nivel ? 1 : 0), 0); ?>
            <span class="badge bg-info bg-opacity-25 text-info px-3 py-2"><?= $label ?>: <?= $total ?></span>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<div class="card glass-card border-0 shadow-lg">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover" id="agendamentosTable">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Unidade</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Prioridade</th>
                        <th>Origem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($agendamento['nome']) ?></strong>
                                <?php if (!empty($agendamento['documento'])): ?>
                                    <div class="text-secondary small">Doc.: <?= htmlspecialchars($agendamento['documento']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($agendamento['servico_nome'] ?? '—') ?></td>
                            <td>
                                <?= htmlspecialchars($agendamento['unidade_nome'] ?? '—') ?>
                                <?php if (!empty($agendamento['orgao_nome'])): ?>
                                    <div class="text-secondary small"><?= htmlspecialchars($agendamento['orgao_nome']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= $agendamento['data_agendada'] ? date('d/m/Y', strtotime($agendamento['data_agendada'])) : '—' ?></td>
                            <td><?= $agendamento['hora_agendada'] ?? '—' ?></td>
                            <td>
                                <span class="badge bg-gradient priority-<?= htmlspecialchars($agendamento['prioridade_tipo']) ?>">
                                    <?= strtoupper(htmlspecialchars($agendamento['prioridade_tipo'])) ?>
                                </span>
                            </td>
                            <td><?= strtoupper(htmlspecialchars($agendamento['origem'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="novoAgendamento">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Novo agendamento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <form method="post" action="<?= htmlspecialchars($url('agendamentos/salvar'), ENT_QUOTES) ?>" class="offcanvas-body d-flex flex-column gap-3">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <div class="row g-3">
            <div class="col-12">
                <label for="nome" class="form-label">Nome completo</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="col-md-6">
                <label for="documento" class="form-label">Documento</label>
                <input type="text" class="form-control" id="documento" name="documento">
            </div>
            <div class="col-md-6">
                <label for="contato" class="form-label">Contato</label>
                <input type="text" class="form-control" id="contato" name="contato">
            </div>
            <div class="col-md-6">
                <label for="prioridade_tipo" class="form-label">Prioridade</label>
                <select class="form-select" id="prioridade_tipo" name="prioridade_tipo">
                    <option value="padrao">Padrão</option>
                    <option value="preferencial">Preferencial</option>
                    <option value="80+">80+</option>
                    <option value="servico">Serviço específico</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="categoria" class="form-label">Categoria</label>
                <select class="form-select" id="categoria" name="categoria">
                    <option value="">Selecione</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['nome']) ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="servico_id" class="form-label">Serviço</label>
                <select class="form-select" id="servico_id" name="servico_id" required>
                    <option value="">Selecione</option>
                    <?php foreach ($servicos as $servico): ?>
                        <option value="<?= htmlspecialchars($servico['id']) ?>"><?= htmlspecialchars($servico['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="unidade_id" class="form-label">Unidade</label>
                <select class="form-select" id="unidade_id" name="unidade_id">
                    <option value="">Selecione</option>
                    <?php foreach ($unidades as $unidade): ?>
                        <option value="<?= htmlspecialchars($unidade['id']) ?>"><?= htmlspecialchars($unidade['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="data_agendada" class="form-label">Data</label>
                <input type="date" class="form-control" id="data_agendada" name="data_agendada" required>
            </div>
            <div class="col-md-6">
                <label for="hora_agendada" class="form-label">Horário</label>
                <input type="time" class="form-control" id="hora_agendada" name="hora_agendada" required>
            </div>
            <div class="col-12">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Detalhes adicionais"></textarea>
            </div>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-info btn-lg">Salvar agendamento</button>
        </div>
    </form>
</div>
