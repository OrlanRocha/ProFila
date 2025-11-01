<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card bg-dark border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 text-white mb-3"><?= $usuario ? 'Editar usuário' : 'Novo usuário' ?></h1>
                <form method="post" action="<?= htmlspecialchars($url('usuarios/save'), ENT_QUOTES) ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="id" value="<?= (int) ($usuario['id'] ?? 0) ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nome">Nome</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" name="nome" id="nome" required value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">E-mail</label>
                            <input type="email" class="form-control bg-dark text-white border-secondary" name="email" id="email" required value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="papel">Papel</label>
                            <select class="form-select bg-dark text-white border-secondary" name="papel" id="papel">
                                <?php $papeis = ['admin' => 'Administrador', 'gestor' => 'Gestor', 'atendente' => 'Atendente', 'visor' => 'Visor']; ?>
                                <?php foreach ($papeis as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= (($usuario['papel'] ?? 'atendente') === $key) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (!$usuario): ?>
                            <div class="col-md-6">
                                <label class="form-label" for="senha">Senha inicial</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" name="senha" id="senha" placeholder="Gerada automaticamente se vazio">
                            </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="1" name="ativo" id="ativo" <?= ($usuario['ativo'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ativo">Usuário ativo</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($url('usuarios/index'), ENT_QUOTES) ?>">Voltar</a>
                        <button type="submit" class="btn btn-info">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
