<?php
$isEdit = ($mode ?? '') === 'edit';
$u = $editUser ?? [];
?>
<div class="flex items-center justify-between mb-4">
  <div>
    <div class="text-sm text-slate-400">Administração</div>
    <div class="text-xl font-bold"><?= $isEdit ? 'Editar usuário' : 'Novo usuário' ?></div>
  </div>
  <a href="/usuarios" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 font-semibold">Voltar</a>
</div>

<div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
  <form id="userForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <input type="hidden" name="id" value="<?= (int)($u['id'] ?? 0) ?>">

    <div class="md:col-span-2 flex items-center gap-3">
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="ativo" value="1" <?= (int)($u['ativo'] ?? 1) === 1 ? 'checked' : '' ?>>
        <span>Ativo</span>
      </label>

      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="must_change_password" value="1" <?= (int)($u['must_change_password'] ?? 0) === 1 ? 'checked' : '' ?>>
        <span>Trocar senha no próximo login</span>
      </label>
    </div>

    <div>
      <label class="text-sm text-slate-300">Nome</label>
      <input name="nome" required value="<?= htmlspecialchars($u['nome'] ?? '') ?>"
             class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-indigo-500"/>
    </div>

    <div>
      <label class="text-sm text-slate-300">Email</label>
      <input name="email" type="email" required value="<?= htmlspecialchars($u['email'] ?? '') ?>"
             class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-indigo-500"/>
    </div>

    <div>
      <label class="text-sm text-slate-300">CPF</label>
      <input name="cpf" required value="<?= htmlspecialchars($u['cpf'] ?? '') ?>"
             class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-indigo-500"/>
    </div>

    <div>
      <label class="text-sm text-slate-300">Perfil (Role)</label>
      <select name="role_id" class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2">
        <?php foreach (($roles ?? []) as $r): ?>
          <option value="<?= (int)$r['id'] ?>" <?= (int)($u['role_id'] ?? 0) === (int)$r['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($r['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php if (!$isEdit): ?>
      <div class="md:col-span-2">
        <label class="text-sm text-slate-300">Senha inicial</label>
        <input name="password" type="password" required
               class="w-full mt-1 rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-indigo-500"/>
      </div>
    <?php endif; ?>

    <div class="md:col-span-2 flex gap-2 justify-end mt-2">
      <button type="button" id="btnSave"
              class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-semibold">
        Salvar
      </button>
    </div>
  </form>
</div>

<script type="module" src="/assets/js/pages/users-form.js"></script>
