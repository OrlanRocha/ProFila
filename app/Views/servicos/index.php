<?php
/** @var array $servicos */
/** @var array $categorias */
/** @var callable $url */
/** @var string $token */
?>
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
        <h1 class="h3 fw-semibold text-white">Portfólio de Serviços</h1>
        <p class="text-secondary mb-0">Centralize os serviços disponíveis para agendamento, totem e atendimento presencial.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-lg-0">
        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#novaCategoriaModal">
            <i class="bi bi-folder-plus me-2"></i>Nova categoria
        </button>
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#novoServicoModal">
            <i class="bi bi-plus-lg me-2"></i>Novo serviço
        </button>
    </div>
</div>
<div class="card glass-card border-0 shadow-lg">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle" id="servicosTable">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Categoria</th>
                        <th>Duração média</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicos as $servico): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($servico['nome']) ?></strong>
                                <?php if (!empty($servico['descricao'])): ?>
                                    <div class="text-secondary small"><?= htmlspecialchars($servico['descricao']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($servico['categoria_nome'] ?? 'Sem categoria') ?></td>
                            <td><?= $servico['duracao_minutos'] ? htmlspecialchars($servico['duracao_minutos']) . ' min' : '—' ?></td>
                            <td>
                                <span class="badge <?= (int) $servico['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= (int) $servico['ativo'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="novoServicoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0">
                <h1 class="modal-title fs-5">Cadastrar serviço</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="post" action="<?= htmlspecialchars($url('servicos/salvar'), ENT_QUOTES) ?>" autocomplete="off">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria_id" name="categoria_id">
                                <option value="">Sem categoria</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= htmlspecialchars($categoria['id']) ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="duracao_minutos" class="form-label">Duração média (minutos)</label>
                            <input type="number" class="form-control" id="duracao_minutos" name="duracao_minutos" min="0" step="5" value="15">
                        </div>
                        <div class="col-12">
                            <label for="nome" class="form-label">Nome do serviço</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-12">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Detalhe o escopo do atendimento"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Salvar serviço</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="novaCategoriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0">
                <h1 class="modal-title fs-5">Nova categoria</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= htmlspecialchars($url('servicos/salvar'), ENT_QUOTES) ?>" autocomplete="off">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="tipo" value="categoria">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoria_nome" class="form-label">Nome da categoria</label>
                        <input type="text" class="form-control" id="categoria_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria_descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="categoria_descricao" name="descricao" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Salvar categoria</button>
                </div>
            </form>
        </div>
    </div>
</div>
