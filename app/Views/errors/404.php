<div class="text-center py-5">
    <h1 class="display-4">404</h1>
    <p class="lead">Página não encontrada.</p>
    <p class="text-muted"><?= htmlspecialchars($message ?? 'O recurso solicitado não está disponível.') ?></p>
    <a class="btn btn-primary" href="<?= htmlspecialchars($url('dashboard/index'), ENT_QUOTES) ?>">Voltar ao início</a>
</div>
