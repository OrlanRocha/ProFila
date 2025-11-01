<?php use App\Core\Csrf; use App\Core\Session; ?>
<?php $session = new Session(); ?>
<?php $token = $token ?? Csrf::token($session); ?>

<div class="login-viewport">
    <div class="container py-5">
        <div class="login-shell shadow-xxl">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-5 login-branding d-flex flex-column">
                    <div class="login-branding-top">
                        <span class="brand-pill">ProFila — Governo Digital</span>
                        <h1 class="display-6 fw-bold mt-4 mb-3">Experiência fluída para cidadãos e equipes de atendimento.</h1>
                        <p class="text-white-50 mb-4">Coordene filas, agendamentos e painéis individuais por unidade organizacional (UO I, II e III) com métricas em tempo real e auditoria completa.</p>
                    </div>
                    <div class="mt-auto">
                        <ul class="login-feature-list">
                            <li>
                                <i class="bi bi-graph-up-arrow"></i>
                                Indicadores e dashboards operacionais em minutos.
                            </li>
                            <li>
                                <i class="bi bi-pc-display"></i>
                                Painéis identificados por IP com regras de órgão e cliente.
                            </li>
                            <li>
                                <i class="bi bi-calendar-week"></i>
                                Agendamento omnichannel e totem com seleção de prioridade.
                            </li>
                        </ul>
                        <div class="login-meta mt-4">
                            <span class="status-chip online"><span class="bullet"></span>Sessão Segura TLS 1.3</span>
                            <span class="status-chip neutral"><i class="bi bi-shield-check"></i>Log de auditoria contínuo</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 login-form-wrapper">
                    <div class="login-form card h-100 border-0">
                        <div class="card-body p-4 p-md-5 d-flex flex-column">
                            <div class="mb-4 text-center text-md-start">
                                <span class="badge rounded-pill bg-opacity-10 bg-info text-info fw-semibold">Portal Corporativo</span>
                                <h2 class="h1 fw-semibold mt-3 mb-2">Bem-vindo de volta</h2>
                                <p class="text-secondary mb-0">Autentique-se para gerenciar filas, guichês e agendamentos.</p>
                            </div>
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            <form method="post" autocomplete="off" class="d-flex flex-column gap-4">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id="email" required autofocus placeholder="usuario@orgao.gov.br">
                                    <label for="email">E-mail institucional</label>
                                </div>
                                <div>
                                    <label class="form-label text-uppercase small fw-semibold" for="senha">Senha</label>
                                    <div class="input-group input-group-lg rounded-4 overflow-hidden">
                                        <input type="password" class="form-control" name="senha" id="senha" required placeholder="••••••••">
                                        <button class="btn btn-outline-light" type="button" id="togglePassword" aria-label="Mostrar senha"><i class="bi bi-eye"></i></button>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 align-items-sm-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="lembrar">
                                        <label class="form-check-label text-secondary" for="lembrar">Manter sessão nesta estação</label>
                                    </div>
                                    <a href="#" class="link-offset-2 text-info small">Problemas para acessar? Contate o suporte</a>
                                </div>
                                <button type="submit" class="btn btn-info btn-lg shadow-lg">Entrar no sistema</button>
                            </form>
                            <div class="mt-5 small text-secondary d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-lock text-info"></i>
                                    <span>Permissões refinadas por perfil e ponto de atendimento.</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-cpu text-info"></i>
                                    <span>Compatível com hospedagem compartilhada e rotas amigáveis.</span>
                                </div>
                            </div>
                            <div class="mt-auto pt-4 small text-muted text-center text-md-start">
                                © <?= date('Y') ?> ProFila — Senhas &amp; Atendimento Moderno
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="login-orb login-orb-one"></div>
            <div class="login-orb login-orb-two"></div>
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
