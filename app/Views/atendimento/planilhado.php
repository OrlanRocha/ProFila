<section class="space-y-6">
    <div>
        <h2 class="text-2xl font-semibold">📑 Atendimento Planilhado</h2>
        <p class="text-gray-600">Registre manualmente ou importe a planilha de atendimentos realizados no dia.</p>
    </div>

    <div class="bg-white shadow rounded p-4 space-y-4">
        <form id="manualForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome do cidadão</label>
                <input name="cidadao" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Documento</label>
                <input name="documento" class="mt-1 w-full border rounded px-3 py-2" placeholder="CPF/RG">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Serviço/Fila</label>
                <input name="queue_id" class="mt-1 w-full border rounded px-3 py-2" placeholder="ID ou nome" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Prioridade</label>
                <select name="prioridade" class="mt-1 w-full border rounded px-3 py-2">
                    <option value="normal">Normal</option>
                    <option value="preferencial">Preferencial</option>
                    <option value="urgente">Urgente</option>
                </select>
            </div>
            <div class="md:col-span-2 flex justify-end space-x-2">
                <button type="reset" class="px-4 py-2 rounded border">Limpar</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Registrar</button>
            </div>
        </form>

        <div class="border-t pt-4">
            <h3 class="font-semibold mb-2">Importar planilha (CSV)</h3>
            <input type="file" id="csvFile" accept=".csv" class="block w-full border rounded px-3 py-2">
            <p class="text-sm text-gray-500 mt-1">Cabeçalho esperado: cidadao,documento,queue_id,prioridade</p>
            <button id="btnImport" class="mt-2 bg-gray-800 text-white px-4 py-2 rounded">Importar</button>
        </div>
    </div>
</section>

<script type="module">
import { apiPost } from '/assets/js/api.js';

const manualForm = document.querySelector('#manualForm');
manualForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(manualForm));
    const res = await apiPost('/api/tickets/create', payload);
    res.ok ? toastr.success('Atendimento registrado') : toastr.error(res.msg || 'Falha ao registrar');
});

const csvInput = document.querySelector('#csvFile');
const btnImport = document.querySelector('#btnImport');
btnImport?.addEventListener('click', async () => {
    if (!csvInput?.files?.length) {
        toastr.warning('Selecione um arquivo CSV');
        return;
    }
    const file = csvInput.files[0];
    const text = await file.text();
    const lines = text.split('\n').filter(Boolean);
    let imported = 0;
    for (const line of lines.slice(1)) {
        const [cidadao, documento, queue_id, prioridade] = line.split(',');
        if (!cidadao) continue;
        await apiPost('/api/tickets/create', { cidadao, documento, queue_id, prioridade });
        imported++;
    }
    toastr.success(`${imported} registros importados`);
});
</script>
