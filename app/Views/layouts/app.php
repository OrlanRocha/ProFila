<?php
// Variáveis esperadas: $title, $content, $user (array), $permissions (array), $app (array), $flash (opcional)
//
// Helpers simples:
$permissions = $permissions ?? [];
$can = function(string $perm) use ($permissions) {
  return in_array($perm, $permissions, true);
};
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'ProFila') ?></title>

  <script src="https://cdn.tailwindcss.com"></script>

  <!-- jQuery + DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"/>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- App JS -->
  <script type="module" src="/assets/js/app.js"></script>

  <style>
    .dt-container { color: #e5e7eb; }
    table.dataTable thead th { color: #e5e7eb; }
  </style>
</head>

<body class="bg-slate-950 text-slate-100">
<script>
  window.PROFILA = {
    baseUrl: <?= json_encode($app['baseUrl'] ?? '') ?>,
    wsUrl: <?= json_encode($app['wsUrl'] ?? '') ?>,
    csrf: <?= json_encode($app['csrf'] ?? '') ?>
  };

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

<div class="min-h-screen flex">
  <!-- Sidebar -->
  <aside class="w-64 hidden md:flex flex-col bg-slate-900/40 border-r border-slate-800">
    <div class="p-5">
      <div class="text-xl font-extrabold tracking-tight">ProFila</div>
      <div class="text-xs text-slate-400 mt-1">Gestão de filas • Painel de senhas</div>
    </div>

    <nav class="px-3 pb-6 space-y-1">
      <?php if ($can('ticket.create')): ?>
        <a class="block px-3 py-2 rounded-xl hover:bg-slate-800/60" href="/recepcao">Recepção</a>
      <?php endif; ?>
      <?php if ($can('ticket.next')): ?>
        <a class="block px-3 py-2 rounded-xl hover:bg-slate-800/60" href="/atendimento">Atendimento</a>
      <?php endif; ?>
      <?php if ($can('report.view')): ?>
        <a class="block px-3 py-2 rounded-xl hover:bg-slate-800/60" href="/dashboards">Dashboards</a>
      <?php endif; ?>
      <?php if ($can('user.view')): ?>
        <a class="block px-3 py-2 rounded-xl hover:bg-slate-800/60" href="/usuarios">Usuários</a>
      <?php endif; ?>
      <?php if ($can('audit.view')): ?>
        <a class="block px-3 py-2 rounded-xl hover:bg-slate-800/60" href="/auditoria">Auditoria</a>
      <?php endif; ?>
    </nav>

    <div class="mt-auto p-4 border-t border-slate-800">
      <div class="text-sm font-semibold"><?= htmlspecialchars($user['nome'] ?? 'Usuário') ?></div>
      <div class="text-xs text-slate-400"><?= htmlspecialchars($user['email'] ?? '') ?></div>

      <form method="POST" action="/logout" class="mt-3">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($app['csrf'] ?? '') ?>">
        <button class="w-full px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-sm font-semibold">
          Sair
        </button>
      </form>
    </div>
  </aside>

  <!-- Conteúdo -->
  <main class="flex-1">
    <header class="px-6 py-4 border-b border-slate-800 bg-slate-900/20 flex items-center justify-between">
      <div>
        <div class="text-lg font-bold"><?= htmlspecialchars($title ?? '') ?></div>
        <div class="text-xs text-slate-400">Ambiente: <?= htmlspecialchars($app['env'] ?? 'local') ?></div>
      </div>
      <div class="text-xs text-slate-400">
        WS: <?= htmlspecialchars($app['wsUrl'] ?? '') ?>
      </div>
    </header>

    <div class="p-6">
      <?= $yield ?? $content ?? '' ?>
    </div>
  </main>
</div>
</body>
</html>
