<div class="text-center py-5">
    <h1 class="display-4">500</h1>
    <p class="lead">Ocorreu um erro inesperado.</p>
    <p class="text-muted"><?= htmlspecialchars($message ?? 'Tente novamente mais tarde.') ?></p>
    <?php if (!empty($trace)): ?>
        <pre class="text-start bg-light p-3 rounded border overflow-auto" style="max-height: 300px; font-size: 0.875rem;"><?= htmlspecialchars($trace) ?></pre>
    <?php endif; ?>
    <a class="btn btn-primary" href="index.php?r=dashboard/index">Voltar ao início</a>
</div>
