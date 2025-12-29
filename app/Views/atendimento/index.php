<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <!-- Contexto -->
  <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
    <div class="text-sm text-slate-400">Contexto</div>
    <div class="text-lg font-bold mt-1">Mesa / Guichê</div>

    <div class="mt-4 space-y-3">
      <div>
        <label class="text-sm text-slate-300">Fila</label>
        <select id="queueId" class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2">
          <?php foreach (($queues ?? []) as $q): ?>
            <?php $qid = is_array($q) ? (int)($q['id'] ?? $q['queue_id'] ?? 0) : (int)($q->id ?? 0); ?>
            <option value="<?= $qid ?>"><?= htmlspecialchars(is_array($q) ? ($q['name'] ?? $q['nome'] ?? '') : ($q->name ?? $q->nome ?? '')) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="text-sm text-slate-300">Ponto (mesa)</label>
        <select id="pointId" class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2">
          <?php foreach (($points ?? []) as $p): ?>
            <option value="<?= (int)($p['id'] ?? 0) ?>">#<?= (int)($p['numero_ponto'] ?? 0) ?> — <?= htmlspecialchars($p['nome'] ?? '') ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button id="btnNext" class="w-full px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-semibold">
        Próxima senha
      </button>
    </div>
  </div>

  <!-- Senha Atual -->
  <div class="lg:col-span-2 bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-400">Senha atual</div>
        <div id="currentDisplay" class="text-5xl font-extrabold tracking-tight mt-1">—</div>
      </div>
      <div class="text-right">
        <div class="text-sm text-slate-400">Status</div>
        <div id="currentStatus" class="text-lg font-bold">—</div>
      </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
      <button id="btnStart" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 font-semibold" disabled>Iniciar</button>
      <button id="btnFinish" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 font-semibold" disabled>Finalizar</button>
      <button id="btnRecall" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 font-semibold" disabled>Rechamar</button>

      <button id="btnCancel" class="px-4 py-2 rounded-xl bg-rose-600/70 hover:bg-rose-600 font-semibold" disabled>Cancelar</button>
      <button id="btnReinsert" class="px-4 py-2 rounded-xl bg-amber-500/70 hover:bg-amber-500 font-semibold" disabled>Reinserir</button>
    </div>

    <div class="mt-6 bg-slate-950 border border-slate-800 rounded-2xl p-4">
      <div class="text-sm text-slate-400">Últimas ações</div>
      <ul id="feed" class="mt-2 space-y-2 text-sm text-slate-200"></ul>
    </div>
  </div>
</div>

<script type="module" src="/assets/js/pages/atendimento.js"></script>
