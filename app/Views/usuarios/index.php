<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 text-white mb-0">Usuários</h1>
        <p class="text-secondary mb-0">Gerencie contas, perfis e acessos ao sistema.</p>
    </div>
    <a class="btn btn-info" href="<?= htmlspecialchars($url('usuarios/form'), ENT_QUOTES) ?>"><i class="bi bi-plus-circle me-2"></i>Novo usuário</a>
</div>
<div class="card bg-dark border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle" id="usuariosTable">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Papel</th>
                    <th>Status</th>
                    <th>Último acesso</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr data-id="<?= (int) $usuario['id'] ?>">
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><span class="badge text-bg-secondary text-uppercase"><?= htmlspecialchars($usuario['papel']) ?></span></td>
                        <td><?= $usuario['ativo'] ? '<span class="badge text-bg-success">Ativo</span>' : '<span class="badge text-bg-secondary">Inativo</span>' ?></td>
                        <td><?= $usuario['ultimo_login'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_login'])) : '-' ?></td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-light" href="<?= htmlspecialchars($url('usuarios/form', ['id' => (int) $usuario['id']]), ENT_QUOTES) ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" data-action="reset" data-id="<?= (int) $usuario['id'] ?>">
                                    <i class="bi bi-key"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="<?= (int) $usuario['id'] ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<form id="usuarioActions" method="post" class="d-none" data-delete="<?= htmlspecialchars($url('usuarios/delete'), ENT_QUOTES) ?>" data-reset="<?= htmlspecialchars($url('usuarios/reset'), ENT_QUOTES) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="id" value="">
</form>
<script>
    $(function () {
        $('#usuariosTable').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
            dom: 'Bfrtip',
            buttons: ['csv', 'print']
        });

        $('#usuariosTable').on('click', 'button[data-action="delete"]', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Remover usuário?',
                text: 'Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('usuarioActions');
                    form.action = form.dataset.delete;
                    form.querySelector('input[name="id"]').value = id;
                    form.submit();
                }
            });
        });

        $('#usuariosTable').on('click', 'button[data-action="reset"]', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Resetar senha?',
                text: 'Uma nova senha aleatória será gerada.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, resetar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('usuarioActions');
                    form.action = form.dataset.reset;
                    form.querySelector('input[name="id"]').value = id;
                    form.submit();
                }
            });
        });
    });
</script>
