<?php
/** @var array $displays */
/** @var array $unidades */
/** @var array $orgaos */
/** @var array $clientes */
/** @var array $regras */
/** @var array $uoI */
/** @var array $uoII */
/** @var array $uoIII */
/** @var callable $url */
/** @var string $token */
?>
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
        <div class="filter-toolbar mb-4">
            <form id="displayFilters" class="row g-3 align-items-end" onsubmit="return false;">
                <div class="col-md-3">
                    <label class="form-label" for="filtroPainelOrgao">Órgão</label>
                    <select class="form-select bg-dark" id="filtroPainelOrgao" data-display-filter="orgao">
                        <option value="">Todos</option>
                        <?php foreach ($orgaos as $orgao): ?>
                            <option value="<?= (int) $orgao['id'] ?>"><?= htmlspecialchars($orgao['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="filtroPainelCliente">Cliente</label>
                    <select class="form-select bg-dark" id="filtroPainelCliente" data-display-filter="cliente">
                        <option value="">Todos</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= (int) $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filtroPainelUoI">UO nível I</label>
                    <select class="form-select bg-dark" id="filtroPainelUoI" data-display-filter="uo_i">
                        <option value="">Todas</option>
                        <?php foreach ($uoI as $uo): ?>
                            <option value="<?= (int) $uo['id'] ?>"><?= htmlspecialchars($uo['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filtroPainelUoII">UO nível II</label>
                    <select class="form-select bg-dark" id="filtroPainelUoII" data-display-filter="uo_ii">
                        <option value="">Todas</option>
                        <?php foreach ($uoII as $uo): ?>
                            <option value="<?= (int) $uo['id'] ?>"><?= htmlspecialchars($uo['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filtroPainelUoIII">UO nível III</label>
                    <select class="form-select bg-dark" id="filtroPainelUoIII" data-display-filter="uo_iii">
                        <option value="">Todas</option>
                        <?php foreach ($uoIII as $uo): ?>
                            <option value="<?= (int) $uo['id'] ?>"><?= htmlspecialchars($uo['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filtroPainelStatus">Status</label>
                    <select class="form-select bg-dark" id="filtroPainelStatus" data-display-filter="status">
                        <option value="">Todos</option>
                        <option value="ativo">Ativo</option>
                        <option value="pendente">Pendente</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="button" class="btn btn-primary flex-grow-1" id="aplicarFiltroPainel"><i class="bi bi-search"></i> Pesquisar</button>
                    <button type="button" class="btn btn-outline-light" id="limparFiltroPainel">Limpar</button>
                </div>
            </form>
        </div>
        <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
            <table class="table table-dark table-striped align-middle mb-0" id="tabelaDisplays">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Apelido</th>
                        <th>Órgão</th>
                        <th>Cliente</th>
                        <th>Unidade</th>
                        <th>UO I</th>
                        <th>UO II</th>
                        <th>UO III</th>
                        <th>Regra</th>
                        <th>Status</th>
                        <th>Último visto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($displays as $display): ?>
                    <tr data-orgao="<?= (int) ($display['orgao_id'] ?? 0) ?>" data-cliente="<?= (int) ($display['cliente_id'] ?? 0) ?>" data-uo-i="<?= (int) ($display['uo_nivel_i_id'] ?? 0) ?>" data-uo-ii="<?= (int) ($display['uo_nivel_ii_id'] ?? 0) ?>" data-uo-iii="<?= (int) ($display['uo_nivel_iii_id'] ?? 0) ?>" data-status="<?= htmlspecialchars($display['status']) ?>">
                        <td class="fw-semibold text-info"><?= htmlspecialchars($display['ip_address']) ?></td>
                        <td><?= htmlspecialchars($display['apelido'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($display['orgao_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['cliente_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['unidade_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['uo_nivel_i_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['uo_nivel_ii_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['uo_nivel_iii_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($display['regra_nome'] ?? 'Padrão') ?></td>
                        <td>
                            <?php $status = strtolower($display['status'] ?? 'pendente'); ?>
                            <span class="status-chip <?= $status === 'ativo' ? 'online' : ($status === 'pendente' ? 'pending' : 'offline') ?>">
                                <span class="bullet"></span><?= strtoupper($status) ?>
                            </span>
                        </td>
                        <td><?= $display['ultimo_visto'] ? date('d/m/Y H:i', strtotime($display['ultimo_visto'])) : '—' ?></td>
                        <td>
                            <form method="post" action="<?= htmlspecialchars($url('painel/salvar-display'), ENT_QUOTES) ?>" class="row g-2 align-items-center">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                                <input type="hidden" name="display_id" value="<?= (int) $display['id'] ?>">
                                <div class="col-12 col-xl-4">
                                    <select name="unidade_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                        <option value="">Sem unidade</option>
                                        <?php foreach ($unidades as $unidade): ?>
                                            <option value="<?= (int) $unidade['id'] ?>" <?= (int) ($display['unidade_id'] ?? 0) === (int) $unidade['id'] ? 'selected' : '' ?>><?= htmlspecialchars($unidade['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-xl-3">
                                    <select name="regra_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                        <option value="">Padrão</option>
                                        <?php foreach ($regras as $regra): ?>
                                            <option value="<?= (int) $regra['id'] ?>" <?= (int) ($display['regra_id'] ?? 0) === (int) $regra['id'] ? 'selected' : '' ?>><?= htmlspecialchars($regra['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-xl-2">
                                    <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary">
                                        <option value="pendente" <?= $status === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="ativo" <?= $status === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                        <option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                </div>
                                <div class="col-6 col-xl-2">
                                    <input type="text" name="apelido" value="<?= htmlspecialchars($display['apelido'] ?? '') ?>" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Apelido">
                                </div>
                                <div class="col-6 col-xl-1 text-end">
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
    document.addEventListener('DOMContentLoaded', () => {
        const tableElement = document.getElementById('tabelaDisplays');
        if (!tableElement || !window.jQuery) {
            return;
        }
        const $ = window.jQuery;
        const table = $('#tabelaDisplays').DataTable({
            paging: true,
            searching: true,
            info: false,
            order: [[10, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });

        const filterState = {};
        const filterInputs = document.querySelectorAll('[data-display-filter]');
        const applyFilters = () => {
            filterInputs.forEach((input) => {
                filterState[input.dataset.displayFilter] = input.value;
            });
            table.draw();
        };

        const resetFilters = () => {
            filterInputs.forEach((input) => {
                input.value = '';
                filterState[input.dataset.displayFilter] = '';
            });
            table.search('').draw();
        };

        const filterFn = (settings, data, dataIndex) => {
            if (settings.nTable !== tableElement) {
                return true;
            }
            const node = table.row(dataIndex).node();
            if (!node) {
                return true;
            }
            const matches = [
                ['orgao', Number(node.dataset.orgao || 0)],
                ['cliente', Number(node.dataset.cliente || 0)],
                ['uo_i', Number(node.dataset.uoI || 0)],
                ['uo_ii', Number(node.dataset.uoIi || 0)],
                ['uo_iii', Number(node.dataset.uoIii || 0)],
                ['status', (node.dataset.status || '').toLowerCase()]
            ].every(([key, value]) => {
                const selected = filterState[key];
                if (!selected) {
                    return true;
                }
                if (key === 'status') {
                    return value === selected.toLowerCase();
                }
                return Number(selected) === value;
            });
            return matches;
        };

        window.ProFilaFilters = window.ProFilaFilters || {};
        if (!window.ProFilaFilters.displays) {
            window.ProFilaFilters.displays = filterFn;
            $.fn.dataTable.ext.search.push(filterFn);
        }

        document.getElementById('aplicarFiltroPainel')?.addEventListener('click', applyFilters);
        document.getElementById('limparFiltroPainel')?.addEventListener('click', () => {
            resetFilters();
        });
        filterInputs.forEach((input) => {
            input.addEventListener('change', () => {
                if (input.dataset.displayFilter === 'status') {
                    applyFilters();
                }
            });
        });

        resetFilters();
    });
</script>
