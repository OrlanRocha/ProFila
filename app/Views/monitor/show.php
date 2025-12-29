<?php
$chId = (int)($channelId ?? 0);
$hdr = $channel['nome'] ?? ("Canal #".$chId);
$info = $channel['informativo'] ?? "";
$linhas = (int)($channel['linhas'] ?? 1);
?>
<div class="min-h-screen bg-slate-950 text-white flex flex-col">
  <header class="px-6 py-5 border-b border-slate-800 bg-slate-900/20">
    <div class="text-3xl font-extrabold tracking-tight" id="monitorHeader"><?= htmlspecialchars($hdr) ?></div>
    <div class="text-sm text-slate-300 mt-1" id="monitorSub">Canal <?= $chId ?> • Linhas: <?= $linhas ?></div>
  </header>

  <main class="flex-1 grid grid-cols-1 md:grid-cols-<?= max(1, min(3,$linhas)) ?> gap-4 p-6">
    <?php for ($i=1; $i<=$linhas; $i++): ?>
      <section class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 flex flex-col justify-center items-center">
        <div class="text-lg text-slate-300 font-semibold">Linha <?= $i ?></div>
        <div id="lineDisplay<?= $i ?>" class="text-7xl md:text-8xl font-extrabold mt-3">—</div>
        <div id="linePoint<?= $i ?>" class="text-2xl text-slate-200 font-bold mt-2">—</div>
      </section>
    <?php endfor; ?>
  </main>

  <footer class="px-6 py-4 border-t border-slate-800 bg-slate-900/20">
    <div class="flex items-center justify-between gap-4">
      <div id="footerInfo" class="text-lg text-slate-200 font-semibold truncate">
        <?= htmlspecialchars($info) ?>
      </div>
      <div id="wsStatus" class="text-sm text-slate-400">WS: conectando…</div>
    </div>
  </footer>
</div>

<script>
  window.MONITOR = { channelId: <?= (int)$chId ?>, linhas: <?= (int)$linhas ?> };
</script>
<script type="module" src="/assets/js/pages/monitor.js"></script>
