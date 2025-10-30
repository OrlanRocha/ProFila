<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ProFila') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="app-header">
    <div class="container">
        <h1 class="app-title">ProFila</h1>
        <?php if (isset($user) && !empty($user)): ?>
            <nav class="app-nav">
                <a href="/dashboard">Dashboard</a>
                <a href="/tickets">Atendimentos</a>
                <form action="/logout" method="post" class="logout-form">
                    <button type="submit">Sair</button>
                </form>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container">
    <?= $content ?>
</main>
<footer class="app-footer">
    <div class="container">
        <small>&copy; <?= date('Y') ?> ProFila - Sistema de Painel de Senhas</small>
    </div>
</footer>
<script src="/assets/js/app.js"></script>
</body>
</html>
