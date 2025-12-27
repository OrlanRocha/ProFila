<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">👥 Usuários</h2>
            <p class="text-gray-600">Gerencie contas, perfis e escopos organizacionais.</p>
        </div>
        <button id="btnNewUser" class="bg-indigo-600 text-white px-4 py-2 rounded">Novo usuário</button>
    </div>

    <div class="bg-white shadow rounded p-4">
        <table id="tblUsers" class="min-w-full">
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
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user->active ? '🟢 Ativo' : '🔴 Inativo' ?></td>
                        <td><?= htmlspecialchars($user->name) ?></td>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td><?= htmlspecialchars($user->cpf ?? '--') ?></td>
                        <td><?= htmlspecialchars($user->role) ?></td>
                        <td class="space-x-2 text-sm">
                            <button class="btn-toggle bg-gray-100 px-2 py-1 rounded" data-id="<?= $user->id ?>" data-active="<?= $user->active ? '1' : '0' ?>">
                                <?= $user->active ? 'Inativar' : 'Ativar' ?>
                            </button>
                            <button class="btn-reset bg-gray-100 px-2 py-1 rounded" data-id="<?= $user->id ?>">Reset senha</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div id="modalNew" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow p-6 w-full max-w-lg space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Novo usuário</h3>
            <button id="closeModal" class="text-gray-500">✕</button>
        </div>
        <form id="formNew" class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" name="name" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" name="cpf" class="mt-1 w-full border rounded px-3 py-2" placeholder="000.000.000-00">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Perfil</label>
                <select name="role_id" class="mt-1 w-full border rounded px-3 py-2">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->id ?>"><?= htmlspecialchars($role->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="btnCancel" class="px-4 py-2 rounded border">Cancelar</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script type="module">
    import { apiPost } from '/assets/js/api.js';

    const table = document.querySelector('#tblUsers');
    if (table && window.jQuery) {
        jQuery(table).DataTable({ pageLength: 25 });
    }

    const modal = document.querySelector('#modalNew');
    const openModal = () => modal?.classList.remove('hidden');
    const closeModal = () => modal?.classList.add('hidden');

    document.querySelector('#btnNewUser')?.addEventListener('click', openModal);
    document.querySelector('#closeModal')?.addEventListener('click', closeModal);
    document.querySelector('#btnCancel')?.addEventListener('click', closeModal);

    document.querySelector('#formNew')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const data = Object.fromEntries(new FormData(form));
        const res = await apiPost('/api/users/create', data);
        if (res.ok) {
            toastr.success('Usuário criado');
            window.location.reload();
        } else {
            toastr.error(res.msg || 'Falha ao criar usuário');
        }
    });

    document.querySelectorAll('.btn-toggle').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const active = btn.dataset.active === '1';
            const reason = await Swal.fire({
                title: active ? 'Inativar usuário?' : 'Ativar usuário?',
                input: 'text',
                inputPlaceholder: 'Motivo',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
            });
            if (!reason.isConfirmed) return;
            const res = await apiPost('/api/users/toggle', { id, active: !active, reason: reason.value });
            res.ok ? window.location.reload() : toastr.error(res.msg || 'Falha ao atualizar');
        });
    });

    document.querySelectorAll('.btn-reset').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const reason = await Swal.fire({
                title: 'Resetar senha?',
                input: 'text',
                inputPlaceholder: 'Motivo',
                showCancelButton: true,
            });
            if (!reason.isConfirmed) return;
            const res = await apiPost('/api/users/reset-password', { id, reason: reason.value });
            res.ok ? toastr.success('Senha redefinida') : toastr.error(res.msg || 'Falha ao redefinir');
        });
    });
</script>
