<?php
// Variáveis esperadas: $title, $content, $flash (opcional), $app (opcional)
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'ProFila') ?></title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
</head>
<body class="bg-slate-950 text-white">
  <script>
    window.PROFILA = {
      baseUrl: <?= json_encode($app['baseUrl'] ?? '') ?>,
      wsUrl: <?= json_encode($app['wsUrl'] ?? '') ?>,
      csrf: <?= json_encode($app['csrf'] ?? '') ?>
    };
  </script>
  <?= $yield ?? $content ?? '' ?>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script>
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: "toast-bottom-right",
      timeOut: 3500
    };

    <?php if (!empty($flash['error'] ?? null)): ?>
      toastr.error(<?= json_encode($flash['error']) ?>);
    <?php endif; ?>
    <?php if (!empty($flash['success'] ?? null)): ?>
      toastr.success(<?= json_encode($flash['success']) ?>);
    <?php endif; ?>
  </script>
</body>
</html>
