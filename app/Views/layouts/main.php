<?php
use App\Core\Session;
use App\Core\UrlGenerator;

$session = new Session();
$user = $session->get('user');
$flash = $session->get('flash');
if ($flash) {
    $session->remove('flash');
}
$can = static fn (string $permission): bool => in_array($permission, $user['permissoes'] ?? [], true);
$baseAssets = ($config['app']['base_url'] ?? '');
$basePath = trim(UrlGenerator::basePath($config), '/');
$requested = $_GET['r'] ?? $_GET['path'] ?? trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', '/');
if ($basePath !== '' && str_starts_with($requested, $basePath)) {
    $requested = trim(substr($requested, strlen($basePath)), '/');
}
$isActive = static fn (string $prefix) => str_starts_with($requested, trim($prefix, '/'));
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ProFila</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@5/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseAssets . '/public/assets/css/app.css', ENT_QUOTES) ?>">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0f172a, #1d1b4b); min-height: 100vh; }
        .app-shell { backdrop-filter: blur(12px); background: rgba(15, 23, 42, 0.75); box-shadow: 0 15px 45px rgba(15, 23, 42, 0.5); border-radius: 24px; }
        .navbar-brand span { color: #38bdf8; }
        .sidebar-link { border-radius: 12px; }
        .sidebar-link.active { background: rgba(56, 189, 248, 0.15); color: #38bdf8 !important; }
        main { color: #e2e8f0; }
        footer { color: rgba(226, 232, 240, 0.65); }
    </style>
</head>
<body class="py-4 py-lg-5">
<div class="container-xl">
    <div class="app-shell mx-auto overflow-hidden">
        <div class="row g-0">
            <aside class="col-12 col-lg-3 p-4 border-end border-light border-opacity-10 bg-transparent">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <a href="<?= htmlspecialchars($url('dashboard/index'), ENT_QUOTES) ?>" class="navbar-brand fw-bold fs-4 text-white text-decoration-none">
                        Pro<span>Fila</span>
                    </a>
                    <button class="btn btn-outline-light d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarNav">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
                <div class="collapse d-lg-block" id="sidebarNav">
                    <nav class="nav flex-column gap-2">
                        <?php if ($user): ?>
                            <?php if ($can('dashboard.view')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('dashboard') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('dashboard/index'), ENT_QUOTES) ?>">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            <?php endif; ?>
                            <?php if ($can('relatorios.view')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('relatorios') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('relatorios/dashboard'), ENT_QUOTES) ?>">
                                    <i class="bi bi-graph-up-arrow me-2"></i>Relatórios
                                </a>
                            <?php endif; ?>
                            <?php if ($can('usuarios.manage')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('usuarios') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('usuarios/index'), ENT_QUOTES) ?>">
                                    <i class="bi bi-people me-2"></i>Usuários
                                </a>
                            <?php endif; ?>
                            <?php if ($can('permissoes.manage')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('permissoes') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('permissoes/index'), ENT_QUOTES) ?>">
                                    <i class="bi bi-shield-lock me-2"></i>Permissões
                                </a>
                            <?php endif; ?>
                            <?php if ($can('filas.manage')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('filas') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('filas/index'), ENT_QUOTES) ?>">
                                    <i class="bi bi-collection me-2"></i>Filas
                                </a>
                            <?php endif; ?>
                            <?php if ($can('guiches.manage')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('guiches') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('guiches/index'), ENT_QUOTES) ?>">
                                    <i class="bi bi-grid-3x3-gap me-2"></i>Guichês
                                </a>
                            <?php endif; ?>
                            <?php if ($can('painel.manage')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('painel') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('painel/index'), ENT_QUOTES) ?>">
                                    <i class="bi bi-display me-2"></i>Painéis
                                </a>
                            <?php endif; ?>
                            <?php if ($can('senhas.emit')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('senhas/emitir') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('senhas/emitir'), ENT_QUOTES) ?>">
                                    <i class="bi bi-ticket-perforated me-2"></i>Emitir Senhas
                                </a>
                            <?php endif; ?>
                            <?php if ($can('senhas.operate')): ?>
                                <a class="nav-link text-white sidebar-link <?= $isActive('senhas/operacao') ? 'active' : '' ?>" href="<?= htmlspecialchars($url('senhas/operacao'), ENT_QUOTES) ?>">
                                    <i class="bi bi-broadcast-pin me-2"></i>Operação de Atendimento
                                </a>
                            <?php endif; ?>
                            <a class="nav-link text-white sidebar-link" href="<?= htmlspecialchars($url('painel/display'), ENT_QUOTES) ?>" target="_blank">
                                <i class="bi bi-tv me-2"></i>Painel Público
                            </a>
                        <?php endif; ?>
                    </nav>
                    <div class="mt-4 p-3 bg-dark rounded-4 text-white-50 small">
                        <?php if ($user): ?>
                            <div class="fw-semibold text-white mb-1">Olá, <?= htmlspecialchars($user['nome']) ?></div>
                            <div class="text-uppercase text-secondary">Perfil: <?= htmlspecialchars($user['papel']) ?></div>
                            <a class="btn btn-outline-light btn-sm mt-3" href="<?= htmlspecialchars($url('auth/logout'), ENT_QUOTES) ?>">Sair</a>
                        <?php else: ?>
                            <p>Entre para acessar os módulos de operação e relatórios.</p>
                            <a class="btn btn-info btn-sm" href="<?= htmlspecialchars($url('auth/login'), ENT_QUOTES) ?>">Fazer login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
            <main class="col-12 col-lg-9 p-4 p-lg-5">
                <?php if ($flash): ?>
                    <div class="alert alert-info alert-dismissible fade show shadow" role="alert">
                        <?= htmlspecialchars($flash) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                <?php endif; ?>
                <?= $content ?? '' ?>
            </main>
        </div>
        <footer class="px-4 py-3 text-center small border-top border-light border-opacity-10 bg-transparent">
            &copy; <?= date('Y') ?> ProFila — Sistema de Senhas &amp; Atendimento moderno
        </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="<?= htmlspecialchars($baseAssets . '/public/assets/js/app.js', ENT_QUOTES) ?>"></script>
</body>
</html>
