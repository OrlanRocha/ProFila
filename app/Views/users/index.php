<div class="flex items-center justify-between mb-4">
  <div>
    <div class="text-sm text-slate-400">Administração</div>
    <div class="text-xl font-bold">Usuários</div>
  </div>

  <?php if (in_array('user.manage', $permissions ?? [], true)): ?>
    <a href="/usuarios/novo"
       class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-semibold">
      Novo usuário
    </a>
  <?php endif; ?>
</div>

<div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
  <table id="usersTable" class="display w-full">
    <thead>
      <tr>
        <th>Status</th>
        <th>Nome</th>
        <th>Email</th>
        <th>CPF</th>
        <th>Perfil</th>
        <th>Ações</th>
      </tr>
    </thead>
  </table>
</div>

<script type="module" src="/assets/js/pages/users-index.js"></script>
