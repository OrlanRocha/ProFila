<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">🪑 Atendimento Presencial</h2>
            <p class="text-gray-600">Abra o ponto, selecione o órgão e chame a próxima senha conforme prioridade.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded p-4 space-y-3">
            <h3 class="font-semibold">1) Escolha o órgão</h3>
            <select id="selOrgao" class="w-full border rounded px-3 py-2">
                <option value="">Selecione</option>
                <?php foreach ($orgaos as $orgao): ?>
                    <option value="<?= $orgao['id'] ?>"><?= htmlspecialchars($orgao['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="bg-white shadow rounded p-4 space-y-3">
            <h3 class="font-semibold">2) Abra o ponto</h3>
            <select id="selPonto" class="w-full border rounded px-3 py-2">
                <option value="">Selecione</option>
                <?php foreach ($pontos as $ponto): ?>
                    <option value="<?= $ponto['id'] ?>" data-orgao="<?= $ponto['orgao_id'] ?>"><?= htmlspecialchars($ponto['nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <button id="btnAbrir" class="bg-green-600 text-white px-3 py-2 rounded w-full">Abrir ponto</button>
            <p id="pontoStatus" class="text-sm text-gray-500">Nenhum ponto aberto.</p>
        </div>

        <div class="bg-white shadow rounded p-4 space-y-3">
            <h3 class="font-semibold">3) Chamar senha</h3>
            <select id="selFila" class="w-full border rounded px-3 py-2">
                <option value="">Selecione</option>
                <?php foreach ($queues as $queue): ?>
                    <option value="<?= $queue->id ?>"><?= htmlspecialchars($queue->name) ?> (<?= htmlspecialchars($queue->letter) ?>)</option>
                <?php endforeach; ?>
            </select>
            <button id="btnNext" class="bg-indigo-600 text-white px-3 py-2 rounded w-full">Chamar próxima</button>
            <div id="senhaAtual" class="text-center text-3xl font-bold text-indigo-700"></div>
        </div>
    </div>
</section>

<script type="module">
import { apiPost } from '/assets/js/api.js';

let pontoAberto = null;

const selOrgao = document.querySelector('#selOrgao');
const selPonto = document.querySelector('#selPonto');
const status = document.querySelector('#pontoStatus');
const selFila = document.querySelector('#selFila');
const senhaAtual = document.querySelector('#senhaAtual');

document.querySelector('#btnAbrir')?.addEventListener('click', () => {
    const orgao = selOrgao.value;
    const ponto = selPonto.value;
    if (!orgao || !ponto) {
        toastr.warning('Selecione órgão e ponto.');
        return;
    }
    pontoAberto = ponto;
    status.textContent = `Ponto ${ponto} aberto para órgão ${orgao}.`;
    status.className = 'text-sm text-green-700';
});

document.querySelector('#btnNext')?.addEventListener('click', async () => {
    if (!pontoAberto) {
        toastr.error('Nenhum ponto aberto.');
        return;
    }
    if (!selFila.value) {
        toastr.warning('Selecione uma fila.');
        return;
    }
    const res = await apiPost('/api/tickets/next', { queue_id: selFila.value, point_id: pontoAberto });
    if (!res.ok) {
        toastr.error(res.msg || 'Nenhuma senha disponível.');
        return;
    }
    senhaAtual.textContent = res.data.display ?? res.data.status ?? '---';
});
</script>
