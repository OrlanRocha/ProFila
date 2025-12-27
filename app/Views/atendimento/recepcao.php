<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">🎟️ Recepção</h2>
            <p class="text-gray-600">Emita e imprima senhas por órgão, considerando prioridade do cidadão.</p>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4 space-y-4">
        <form id="formRecepcao" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Órgão</label>
                <select name="orgao_id" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    <?php foreach ($orgaos as $orgao): ?>
                        <option value="<?= $orgao['id'] ?>"><?= htmlspecialchars($orgao['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Fila / Serviço</label>
                <select name="queue_id" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    <?php foreach ($queues as $queue): ?>
                        <option value="<?= $queue->id ?>"><?= htmlspecialchars($queue->name) ?> (<?= htmlspecialchars($queue->letter) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Prioridade</label>
                <select name="prioridade" class="mt-1 w-full border rounded px-3 py-2">
                    <option value="normal">Normal</option>
                    <option value="preferencial">Preferencial</option>
                    <option value="idoso">Idoso</option>
                    <option value="pcd">PCD</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Observações</label>
                <textarea name="obs" class="mt-1 w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>
            <div class="md:col-span-2 flex justify-end space-x-2">
                <button type="reset" class="px-4 py-2 rounded border">Limpar</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Emitir senha</button>
            </div>
        </form>

        <div id="ticketInfo" class="hidden bg-indigo-50 border border-indigo-200 rounded p-3"></div>
    </div>
</section>

<script type="module">
import { apiPost } from '/assets/js/api.js';

const form = document.querySelector('#formRecepcao');
const info = document.querySelector('#ticketInfo');

form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(form));
    const res = await apiPost('/api/tickets/create', payload);
    if (res.ok) {
        const display = res.data?.display ?? '---';
        info.classList.remove('hidden');
        info.innerHTML = `<p class="text-lg font-semibold">Senha emitida: ${display}</p><p class="text-sm text-gray-600">Prioridade: ${payload.prioridade || 'normal'}</p>`;
        toastr.success('Senha emitida');
    } else {
        toastr.error(res.msg || 'Falha ao emitir senha');
    }
});
</script>
