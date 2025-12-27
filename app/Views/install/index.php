<section class="space-y-6">
    <h2 class="text-2xl font-semibold">Instalação do ProFila</h2>
    <p class="text-gray-600">Siga os passos para configurar o ambiente. Cada etapa exibirá um check ao ser concluída.</p>

    <ol class="space-y-3" id="steps">
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="env">
            <div>
                <p class="font-semibold">1) Criar arquivo .env</p>
                <p class="text-sm text-gray-500">Baseado no .env.example</p>
            </div>
            <span class="status text-gray-500">Pendente</span>
        </li>
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="migrate">
            <div>
                <p class="font-semibold">2) Executar migrations</p>
                <p class="text-sm text-gray-500">Cria tabelas principais</p>
            </div>
            <span class="status text-gray-500">Pendente</span>
        </li>
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="seed">
            <div>
                <p class="font-semibold">3) Rodar seeds</p>
                <p class="text-sm text-gray-500">Roles, permissões e usuário admin</p>
            </div>
            <span class="status text-gray-500">Pendente</span>
        </li>
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="finish">
            <div>
                <p class="font-semibold">4) Finalizar</p>
                <p class="text-sm text-gray-500">Bloqueia o instalador e redireciona ao login</p>
            </div>
            <span class="status text-gray-500">Pendente</span>
        </li>
    </ol>

    <button id="btnStart" class="bg-indigo-600 text-white px-4 py-2 rounded">Iniciar instalação</button>
</section>

<script type="module">
    import { apiPost } from '/assets/js/api.js';

    const steps = ['env', 'migrate', 'seed', 'finish'];
    const statuses = {
        pending: 'text-gray-500',
        ok: 'text-green-600',
        fail: 'text-red-600'
    };

    const updateStatus = (step, state, msg = '') => {
        const row = document.querySelector(`[data-step="${step}"] .status`);
        if (!row) return;
        row.className = `status font-semibold ${statuses[state] || ''}`;
        row.textContent = state === 'ok' ? 'Ok' : state === 'fail' ? 'Falhou' : 'Pendente';
        if (msg) row.title = msg;
    };

    const runStep = async (step) => {
        updateStatus(step, 'pending');
        const res = await apiPost('/api/install/step', { step });
        if (res.ok) {
            updateStatus(step, 'ok');
            return true;
        }
        updateStatus(step, 'fail', res.msg || 'Falha na etapa');
        toastr.error(res.msg || `Falha na etapa ${step}`);
        return false;
    };

    document.querySelector('#btnStart')?.addEventListener('click', async () => {
        for (const step of steps) {
            const ok = await runStep(step);
            if (!ok) return;
        }
        toastr.success('Instalação concluída! Redirecionando para login...');
        setTimeout(() => window.location.href = '/login', 1500);
    });
</script>
