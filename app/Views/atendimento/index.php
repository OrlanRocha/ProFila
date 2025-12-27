<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Atendimento presencial</h2>
            <p class="text-gray-600">Chame, rechame e conclua senhas em tempo real.</p>
        </div>
        <button id="btnNext" class="bg-indigo-600 text-white px-4 py-2 rounded">Próxima senha</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-lg font-semibold mb-3">Filas disponíveis</h3>
            <ul class="space-y-2">
                <?php foreach ($queues as $queue): ?>
                    <li class="flex items-center justify-between border rounded p-3">
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($queue->name) ?></p>
                            <p class="text-sm text-gray-500">Letra: <?= htmlspecialchars($queue->letter) ?> · Política: <?= htmlspecialchars($queue->policy) ?></p>
                        </div>
                        <button data-queue="<?= $queue->id ?>" class="queue-btn bg-gray-100 px-3 py-1 rounded">Selecionar</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="bg-white shadow rounded p-4">
            <h3 class="text-lg font-semibold mb-3">Status do guichê</h3>
            <div id="statusPanel" class="text-gray-700">
                <p>Nenhuma senha ativa.</p>
            </div>
        </div>
    </div>
</section>

<script type="module">
    import { apiPost } from '/assets/js/api.js';

    let selectedQueue = null;

    document.querySelectorAll('.queue-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            selectedQueue = btn.dataset.queue;
            toastr.info(`Fila ${btn.dataset.queue} selecionada.`);
        });
    });

    document.querySelector('#btnNext')?.addEventListener('click', async () => {
        if (!selectedQueue) {
            toastr.warning('Selecione uma fila primeiro');
            return;
        }

        const response = await apiPost('/api/tickets/next', {
            queue_id: selectedQueue,
            point_id: 1
        });

        if (!response.ok) {
            toastr.error(response.msg || 'Nenhuma senha encontrada');
            return;
        }

        document.querySelector('#statusPanel').innerHTML = `
            <p class="text-xl font-semibold">${response.data.display}</p>
            <p class="text-gray-600">Status: ${response.data.status}</p>
        `;
    });
</script>
