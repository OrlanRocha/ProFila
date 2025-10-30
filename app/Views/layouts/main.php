<?php
use App\Core\Session;

$session = new Session();
$user = $session->get('user');
$flash = $session->get('flash');
if ($flash) {
    $session->remove('flash');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ProFila</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@5/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars(($config['app']['base_url'] ?? '') . '/public/assets/css/app.css', ENT_QUOTES) ?>">
</head>
<body class="bg-light min-vh-100 d-flex flex-column">
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php?r=dashboard/index">ProFila</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($user): ?>
                    <?php if (in_array($user['papel'], ['admin','gestor'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?r=dashboard/index">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?r=relatorios/dashboard">Relatórios</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?r=usuarios/index">Usuários</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?r=filas/index">Filas</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?r=guiches/index">Guichês</a></li>
                    <?php endif; ?>
                    <?php if (in_array($user['papel'], ['admin','gestor','atendente'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?r=senhas/emitir">Emitir Senhas</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?r=senhas/operacao">Operação</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=painel/display" target="_blank">Painel Público</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <?php if ($user): ?>
                    <span class="text-muted small">Olá, <?= htmlspecialchars($user['nome']) ?> (<?= htmlspecialchars($user['papel']) ?>)</span>
                    <a class="btn btn-outline-danger btn-sm" href="index.php?r=auth/logout">Sair</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="index.php?r=auth/login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="container-fluid flex-grow-1 py-4">
    <?php if ($flash): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    <?= $content ?? '' ?>
</main>
<footer class="bg-white border-top py-3 text-center text-muted small">
    &copy; <?= date('Y') ?> ProFila — Sistema de Senhas &amp; Atendimento
</footer>
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
<script src="<?= htmlspecialchars(($config['app']['base_url'] ?? '') . '/public/assets/js/app.js', ENT_QUOTES) ?>"></script>
</body>
</html>
