<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Usuários</h1>
        <p class="text-muted mb-0">Gerencie contas e permissões de acesso.</p>
    </div>
    <a class="btn btn-primary" href="index.php?r=usuarios/form"><i class="bi bi-plus-circle me-2"></i>Novo usuário</a>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle" id="usuariosTable">
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
                    <tr>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><span class="badge text-bg-secondary text-uppercase"><?= htmlspecialchars($usuario['papel']) ?></span></td>
                        <td>
                            <?php if ($usuario['ativo']): ?>
                                <span class="badge text-bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $usuario['ultimo_login'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_login'])) : '-' ?></td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-primary" href="index.php?r=usuarios/form&id=<?= (int) $usuario['id'] ?>">
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
<form id="usuarioActions" method="post" class="d-none">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="id" value="">
</form>
<script>
    window.ProFila = window.ProFila || {};
    window.ProFila.usuarioToken = '<?= htmlspecialchars($token) ?>';
</script>
