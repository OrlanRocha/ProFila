<div class="login-hero container-fluid">
    <div class="row g-0 align-items-center justify-content-center min-vh-100">
        <div class="col-lg-6 col-xl-5 px-5 py-5">
            <div class="text-white mb-5">
                <span class="badge bg-info bg-opacity-25 text-info mb-3">Sistema de Senhas &amp; Atendimento</span>
                <h1 class="display-5 fw-bold">Organize filas, agendamentos e painéis em um só lugar.</h1>
                <p class="lead text-secondary">O ProFila combina dashboards, totem inteligente e múltiplos painéis por unidade organizacional.</p>
                <div class="d-flex gap-4 mt-4 text-secondary">
                    <div>
                        <div class="h2 text-white mb-1">Tempo real</div>
                        <small>Sincronização instantânea via SSE ou polling configurável.</small>
                    </div>
                    <div>
                        <div class="h2 text-white mb-1">Segurança</div>
                        <small>Perfis, permissões e trilhas de auditoria completas.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-xl-4 px-4 px-lg-5 py-5">
            <div class="card border-0 shadow-xxl glass-card">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h2 class="h4 text-white mb-0">Acesse sua conta</h2>
                        <i class="bi bi-shield-lock text-info fs-3"></i>
                    </div>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" autocomplete="off" class="d-flex flex-column gap-3">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? App\Core\Csrf::token(new App\Core\Session())) ?>">
                        <div>
                            <label class="form-label text-secondary" for="email">E-mail</label>
                            <input type="email" class="form-control form-control-lg" name="email" id="email" required autofocus>
                        </div>
                        <div>
                            <label class="form-label text-secondary" for="senha">Senha</label>
                            <input type="password" class="form-control form-control-lg" name="senha" id="senha" required>
                        </div>
                        <button type="submit" class="btn btn-info btn-lg mt-3">Entrar</button>
                    </form>
                    <div class="text-center text-secondary mt-4 small">
                        Precisa de acesso? Solicite ao administrador do órgão ou unidade.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
