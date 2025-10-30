<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3 text-center">Entrar</h1>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? App\Core\Csrf::token(new App\Core\Session())) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" class="form-control" name="email" id="email" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="senha">Senha</label>
                        <input type="password" class="form-control" name="senha" id="senha" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
