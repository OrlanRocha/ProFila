<section class="space-y-6">
    <h2 class="text-2xl font-semibold">Instalação do ProFila</h2>
    <p class="text-gray-600">Siga os passos para configurar o ambiente. Cada etapa exibirá um check ✅ ao ser concluída.</p>

    <form id="cfgForm" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded shadow">
        <div>
            <label class="block text-sm font-medium text-gray-700">🌐 URL da aplicação</label>
            <input name="app_url" class="mt-1 w-full border rounded px-3 py-2" placeholder="http://localhost/profila" value="http://localhost/profila">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🔧 Ambiente</label>
            <select name="app_env" class="mt-1 w-full border rounded px-3 py-2">
                <option value="local">Local</option>
                <option value="production">Produção</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🗄️ DB Host</label>
            <input name="db_host" class="mt-1 w-full border rounded px-3 py-2" placeholder="127.0.0.1" value="127.0.0.1">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🔌 DB Porta</label>
            <input name="db_port" class="mt-1 w-full border rounded px-3 py-2" placeholder="3306" value="3306">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📚 DB Nome</label>
            <input name="db_name" class="mt-1 w-full border rounded px-3 py-2" placeholder="profila" value="profila">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👤 DB Usuário</label>
            <input name="db_user" class="mt-1 w-full border rounded px-3 py-2" placeholder="root" value="root">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🔑 DB Senha</label>
            <input name="db_pass" type="password" class="mt-1 w-full border rounded px-3 py-2" placeholder="secret" value="secret">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📡 WS Host</label>
            <input name="ws_host" class="mt-1 w-full border rounded px-3 py-2" placeholder="127.0.0.1" value="127.0.0.1">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📡 WS Porta</label>
            <input name="ws_port" class="mt-1 w-full border rounded px-3 py-2" placeholder="8080" value="8080">
        </div>
    </form>

    <ol class="space-y-3" id="steps">
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="env">
            <div>
                <p class="font-semibold">1) Criar arquivo .env</p>
                <p class="text-sm text-gray-500">Baseado no .env.example</p>
            </div>
            <span class="status text-gray-500">🕒 Pendente</span>
        </li>
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="migrate">
            <div>
                <p class="font-semibold">2) Executar migrations</p>
                <p class="text-sm text-gray-500">Cria tabelas principais</p>
            </div>
            <span class="status text-gray-500">🕒 Pendente</span>
        </li>
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="seed">
            <div>
                <p class="font-semibold">3) Rodar seeds</p>
                <p class="text-sm text-gray-500">Roles, permissões e usuário admin</p>
            </div>
            <span class="status text-gray-500">🕒 Pendente</span>
        </li>
        <li class="flex items-center justify-between bg-white p-4 rounded shadow" data-step="finish">
            <div>
                <p class="font-semibold">4) Finalizar</p>
                <p class="text-sm text-gray-500">Bloqueia o instalador e redireciona ao login</p>
            </div>
            <span class="status text-gray-500">🕒 Pendente</span>
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
        row.textContent = state === 'ok' ? '✅ Ok' : state === 'fail' ? '❌ Falhou' : '🕒 Pendente';
        if (msg) row.title = msg;
    };

    const runStep = async (step) => {
        updateStatus(step, 'pending');
        const form = document.querySelector('#cfgForm');
        const data = form ? Object.fromEntries(new FormData(form)) : {};
        const res = await apiPost('/api/install/step', { step, ...data });
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
