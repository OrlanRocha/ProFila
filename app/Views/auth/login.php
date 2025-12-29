<div class="min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-extrabold tracking-tight">ProFila</h1>
        <p class="text-slate-300 text-sm mt-1">Acesse com seu e-mail e senha</p>
      </div>
      <div class="text-xs text-slate-400">v0.1</div>
    </div>

    <form method="POST" action="/login" class="mt-6 space-y-4">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($app['csrf'] ?? '') ?>">

      <div>
        <label class="text-sm text-slate-300">E-mail</label>
        <input name="email" type="email" required
               class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-indigo-500"/>
      </div>

      <div>
        <label class="text-sm text-slate-300">Senha</label>
        <input name="password" type="password" required
               class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-indigo-500"/>
      </div>

      <button class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 px-4 py-2 font-semibold">
        Entrar
      </button>
    </form>

    <div class="mt-4 text-xs text-slate-400">
      Dica: se travar por tentativas, aguarde o tempo de bloqueio.
    </div>
  </div>
</div>
