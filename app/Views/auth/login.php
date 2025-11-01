<?php use App\Core\Csrf; use App\Core\Session; ?>
<?php $session = new Session(); ?>
<?php $token = $token ?? Csrf::token($session); ?>
<div class="login-wrapper">
    <div class="login-card row g-0 shadow-xxl">
        <div class="col-lg-6 login-showcase d-flex flex-column justify-content-between">
            <div>
                <span class="badge bg-dark bg-opacity-25 text-uppercase fw-semibold">Sistema de Senhas &amp; Atendimento</span>
                <h1 class="display-5 fw-bold mt-4 mb-3">Fluxos inteligentes para guichês, painéis e agendamentos.</h1>
                <p class="lead text-white-50">Coordene múltiplos órgãos, unidades (UO I / II / III) e pontos de atendimento em uma plataforma integrada com métricas em tempo real.</p>
            </div>
            <div class="login-badges">
                <div class="login-badge">
                    <i class="bi bi-speedometer2 fs-4"></i>
                    <span>Dashboard operacional e relatórios avançados</span>
                </div>
                <div class="login-badge">
                    <i class="bi bi-pc-display-horizontal fs-4"></i>
                    <span>Painéis identificados por IP com regras dedicadas</span>
                </div>
                <div class="login-badge">
                    <i class="bi bi-calendar-event fs-4"></i>
                    <span>Agendamento omnichannel e totem responsivo</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6 login-panel text-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-semibold mb-0">Acesse sua conta</h2>
                    <span class="text-secondary">Informe suas credenciais corporativas</span>
                </div>
                <span class="status-chip online"><span class="bullet"></span>Segurança TLS</span>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form method="post" autocomplete="off" class="d-flex flex-column gap-3">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                <div>
                    <label class="form-label text-uppercase small" for="email">E-mail institucional</label>
                    <input type="email" class="form-control form-control-lg" name="email" id="email" required autofocus placeholder="usuario@orgao.gov.br">
                </div>
                <div>
                    <label class="form-label text-uppercase small" for="senha">Senha</label>
                    <div class="input-group input-group-lg">
                        <input type="password" class="form-control" name="senha" id="senha" required placeholder="••••••••">
                        <button class="btn btn-outline-light" type="button" id="togglePassword" aria-label="Mostrar senha"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="lembrar">
                        <label class="form-check-label text-secondary" for="lembrar">Manter sessão nesta estação</label>
                    </div>
                    <a href="#" class="text-secondary small">Precisa de ajuda?</a>
                </div>
                <button type="submit" class="btn btn-info btn-lg mt-2">Entrar</button>
            </form>
            <div class="mt-5 small text-secondary">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-shield-lock"></i>
                    <span>Auditoria detalhada de acessos, permissões por função e autenticação com hash seguro.</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('togglePassword');
        const input = document.getElementById('senha');
        if (toggle && input) {
            toggle.addEventListener('click', () => {
                const visible = input.getAttribute('type') === 'text';
                input.setAttribute('type', visible ? 'password' : 'text');
                toggle.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });
        }
    });
</script>
