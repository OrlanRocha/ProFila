<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-semibold text-white">Gestão de Painéis Digitais</h1>
        <p class="text-secondary mb-0">Gerencie displays físicos, atribua unidades, clientes e regras de exibição em tempo real.</p>
    </div>
    <span class="badge bg-info bg-opacity-25 text-info px-4 py-2">IP ativos atualizam automaticamente</span>
</div>
<ul class="nav nav-pills mb-4" id="gestaoTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-displays" data-bs-toggle="pill" data-bs-target="#pane-displays" type="button" role="tab">Displays cadastrados</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-contexto" data-bs-toggle="pill" data-bs-target="#pane-contexto" type="button" role="tab">Contexto institucional</button>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="pane-displays" role="tabpanel">
        <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
            <table class="table table-dark table-striped align-middle mb-0" id="tabelaDisplays">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Apelido</th>
                        <th>Unidade</th>
                        <th>Órgão</th>
                        <th>Cliente</th>
                        <th>Regra</th>
                        <th>Status</th>
                        <th>Último visto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($displays as $display): ?>
                    <tr>
                        <td class="fw-semibold text-info"><?= htmlspecialchars($display['ip_address']) ?></td>
                        <td><?= htmlspecialchars($display['apelido'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($display['unidade_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['orgao_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['cliente_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['regra_nome'] ?? 'Padrão') ?></td>
                        <td>
                            <span class="badge <?= $display['status'] === 'ativo' ? 'bg-success' : ($display['status'] === 'pendente' ? 'bg-warning text-dark' : 'bg-secondary') ?>"><?= htmlspecialchars(strtoupper($display['status'])) ?></span>
                        </td>
                        <td><?= $display['ultimo_visto'] ? date('d/m/Y H:i', strtotime($display['ultimo_visto'])) : '—' ?></td>
                        <td>
                            <form method="post" action="<?= htmlspecialchars($url('painel/salvar-display'), ENT_QUOTES) ?>" class="row g-2 align-items-center">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                                <input type="hidden" name="display_id" value="<?= (int) $display['id'] ?>">
                                <div class="col-12 col-lg-4">
                                    <select name="unidade_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                        <option value="">Sem unidade</option>
                                        <?php foreach ($unidades as $unidade): ?>
                                            <option value="<?= (int) $unidade['id'] ?>" <?= (int) ($display['unidade_id'] ?? 0) === (int) $unidade['id'] ? 'selected' : '' ?>><?= htmlspecialchars($unidade['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <select name="regra_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                        <option value="">Padrão</option>
                                        <?php foreach ($regras as $regra): ?>
                                            <option value="<?= (int) $regra['id'] ?>" <?= (int) ($display['regra_id'] ?? 0) === (int) $regra['id'] ? 'selected' : '' ?>><?= htmlspecialchars($regra['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary">
                                        <option value="pendente" <?= $display['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="ativo" <?= $display['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                        <option value="inativo" <?= $display['status'] === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <input type="text" name="apelido" value="<?= htmlspecialchars($display['apelido'] ?? '') ?>" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Apelido">
                                </div>
                                <div class="col-12 col-lg-1 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-save"></i></button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="pane-contexto" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card bg-dark text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Cadastrar órgão</h2>
                        <form method="post" action="<?= htmlspecialchars($url('painel/criar-contexto'), ENT_QUOTES) ?>" class="row g-3">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                            <input type="hidden" name="tipo" value="orgao">
                            <div class="col-8">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Sigla</label>
                                <input type="text" name="sigla" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-outline-info">Salvar órgão</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-dark text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Cadastrar cliente</h2>
                        <form method="post" action="<?= htmlspecialchars($url('painel/criar-contexto'), ENT_QUOTES) ?>" class="row g-3">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                            <input type="hidden" name="tipo" value="cliente">
                            <div class="col-8">
                                <label class="form-label">Razão social</label>
                                <input type="text" name="nome" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Documento</label>
                                <input type="text" name="documento" class="form-control bg-dark text-white border-secondary">
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-outline-info">Salvar cliente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-dark text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Cadastrar unidade organizacional</h2>
                        <form method="post" action="<?= htmlspecialchars($url('painel/criar-contexto'), ENT_QUOTES) ?>" class="row g-3">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                            <input type="hidden" name="tipo" value="unidade">
                            <div class="col-6">
                                <label class="form-label">Órgão</label>
                                <select name="orgao_id" class="form-select bg-dark text-white border-secondary" required>
                                    <?php foreach ($orgaos as $orgao): ?>
                                        <option value="<?= (int) $orgao['id'] ?>"><?= htmlspecialchars($orgao['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Cliente</label>
                                <select name="cliente_id" class="form-select bg-dark text-white border-secondary" required>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= (int) $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Vinculações por nível</label>
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <select name="uo_nivel_i_id" class="form-select bg-dark text-white border-secondary">
                                            <option value="">UO nível I</option>
                                            <?php foreach ($uoI as $entidade): ?>
                                                <option value="<?= (int) $entidade['id'] ?>"><?= htmlspecialchars($entidade['nome']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select name="uo_nivel_ii_id" class="form-select bg-dark text-white border-secondary">
                                            <option value="">UO nível II</option>
                                            <?php foreach ($uoII as $entidade): ?>
                                                <option value="<?= (int) $entidade['id'] ?>"><?= htmlspecialchars($entidade['nome']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select name="uo_nivel_iii_id" class="form-select bg-dark text-white border-secondary">
                                            <option value="">UO nível III</option>
                                            <?php foreach ($uoIII as $entidade): ?>
                                                <option value="<?= (int) $entidade['id'] ?>"><?= htmlspecialchars($entidade['nome']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <label class="form-label">Nome da unidade</label>
                                <input type="text" name="nome" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Código/UO</label>
                                <input type="text" name="codigo" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-outline-info">Salvar unidade</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-dark text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Nova regra de painel</h2>
                        <form method="post" action="<?= htmlspecialchars($url('painel/criar-contexto'), ENT_QUOTES) ?>" class="row g-3">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                            <input type="hidden" name="tipo" value="regra">
                            <div class="col-6">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Histórico exibido</label>
                                <input type="number" min="1" max="10" name="historico" value="3" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" rows="2" class="form-control bg-dark text-white border-secondary"></textarea>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tema</label>
                                <select name="tema" class="form-select bg-dark text-white border-secondary">
                                    <option value="claro">Claro</option>
                                    <option value="escuro">Escuro</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Intervalo polling (ms)</label>
                                <input type="number" name="polling" value="3000" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-outline-info">Salvar regra</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#tabelaDisplays').DataTable({
            paging: true,
            searching: true,
            info: false,
            order: [[7, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });
    });
</script>
