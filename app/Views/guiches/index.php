<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 text-white mb-0">Guichês & mesas</h1>
        <p class="text-secondary mb-0">Configure prioridades por mesa, modo de chamada e vínculos com unidades.</p>
    </div>
    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#guicheModal"><i class="bi bi-plus-circle me-2"></i>Novo guichê</button>
</div>
<div class="card bg-dark border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle" id="guichesTable">
                <thead>
                <tr>
                    <th>Número</th>
                    <th>Apelido</th>
                    <th>Unidade</th>
                    <th>Fila padrão</th>
                    <th>Modo</th>
                    <th>Prioridades</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($guiches as $guiche): ?>
                    <?php $prioridades = json_decode($guiche['prioridades_config'], true, 512) ?? []; ?>
                    <tr data-guiche='<?= json_encode($guiche, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                        <td><?= (int) $guiche['numero'] ?></td>
                        <td><?= htmlspecialchars($guiche['apelido'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($guiche['unidade_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($guiche['fila_nome'] ?? '-') ?></td>
                        <td class="text-uppercase small text-secondary"><?= htmlspecialchars($guiche['modo_atendimento']) ?></td>
                        <td><?= htmlspecialchars(implode(', ', $prioridades) ?: '—') ?></td>
                        <td><?= $guiche['ativo'] ? '<span class="badge text-bg-success">Ativo</span>' : '<span class="badge text-bg-secondary">Inativo</span>' ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-light me-2" data-action="edit"><i class="bi bi-pencil"></i></button>
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
    <div class="modal-dialog modal-lg">
        <form class="modal-content bg-dark text-white" method="post" action="<?= htmlspecialchars($url('guiches/save'), ENT_QUOTES) ?>">
            <div class="modal-header border-secondary">
                <h2 class="modal-title fs-5" id="guicheModalLabel">Guichê</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="id" id="guicheId">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="form-label" for="guicheNumero">Número</label>
                        <input type="number" class="form-control bg-dark text-white border-secondary" name="numero" id="guicheNumero" required>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="guicheApelido">Apelido</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" name="apelido" id="guicheApelido">
                    </div>
                    <div class="col-lg-5">
                        <label class="form-label" for="guicheUnidade">Unidade</label>
                        <select class="form-select bg-dark text-white border-secondary" name="unidade_id" id="guicheUnidade">
                            <option value="">Sem vínculo</option>
                            <?php foreach ($unidades as $unidade): ?>
                                <option value="<?= (int) $unidade['id'] ?>"><?= htmlspecialchars($unidade['nome']) ?> — <?= htmlspecialchars($unidade['orgao_nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="guicheFila">Fila padrão</label>
                        <select class="form-select bg-dark text-white border-secondary" name="fila_padrao_id" id="guicheFila">
                            <option value="">-- Selecionar --</option>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= (int) $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?> — <?= htmlspecialchars($fila['sigla']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">Modo de chamada</label>
                        <select class="form-select bg-dark text-white border-secondary" name="modo_atendimento" id="guicheModo">
                            <option value="fifo">FIFO - Prioridade pelo peso</option>
                            <option value="sequencial">Sequencial - Ordem absoluta</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Prioridades atendidas</label>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach (['padrao' => 'Padrão', 'preferencial' => 'Preferencial', '80+' => '80+', 'servico' => 'Serviço'] as $valor => $label): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="prioridades[]" id="prioridade-<?= $valor ?>" value="<?= $valor ?>">
                                    <label class="form-check-label" for="prioridade-<?= $valor ?>"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-secondary">Selecione as categorias de senha que esta mesa poderá chamar.</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" id="guicheAtivo" checked>
                            <label class="form-check-label" for="guicheAtivo">Guichê ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-info">Salvar</button>
            </div>
        </form>
    </div>
</div>
<form id="guicheDelete" method="post" class="d-none" action="<?= htmlspecialchars($url('guiches/delete'), ENT_QUOTES) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="id" value="">
</form>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        $('#guichesTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });

        const modal = document.getElementById('guicheModal');
        modal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const prioritiesInputs = modal.querySelectorAll('input[name="prioridades[]"]');
            prioritiesInputs.forEach(input => { input.checked = false; });
            if (button?.dataset.action === 'edit') {
                const data = JSON.parse(button.closest('tr').dataset.guiche);
                document.getElementById('guicheId').value = data.id;
                document.getElementById('guicheNumero').value = data.numero;
                document.getElementById('guicheApelido').value = data.apelido || '';
                document.getElementById('guicheFila').value = data.fila_padrao_id || '';
                document.getElementById('guicheUnidade').value = data.unidade_id || '';
                document.getElementById('guicheModo').value = data.modo_atendimento || 'fifo';
                document.getElementById('guicheAtivo').checked = parseInt(data.ativo, 10) === 1;
                try {
                    const prioridades = JSON.parse(data.prioridades_config ?? '[]');
                    prioritiesInputs.forEach(input => {
                        if (prioridades.includes(input.value)) {
                            input.checked = true;
                        }
                    });
                } catch (error) {
                    prioritiesInputs.forEach(input => input.value === 'padrao' ? input.checked = true : null);
                }
                document.getElementById('guicheModalLabel').textContent = 'Editar guichê';
            } else {
                modal.querySelector('form').reset();
                document.getElementById('guicheId').value = '';
                document.getElementById('guicheModalLabel').textContent = 'Novo guichê';
            }
        });

        document.querySelectorAll('[data-action="edit"]').forEach(button => {
            button.addEventListener('click', () => {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                modalInstance.show(button);
            });
        });

        document.querySelectorAll('[data-action="delete"]').forEach(button => {
            button.addEventListener('click', () => {
                const form = document.getElementById('guicheDelete');
                form.querySelector('input[name="id"]').value = button.dataset.id;
                Swal.fire({
                    title: 'Remover guichê?',
                    text: 'Essa ação remove também as permissões vinculadas à mesa.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, remover',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
