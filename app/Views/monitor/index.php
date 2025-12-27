<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Painel do canal <?= htmlspecialchars($channelId) ?></h2>
            <p class="text-gray-600">Últimas chamadas são exibidas em tempo real via WebSocket.</p>
        </div>
    </div>

    <div id="calls" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
</section>

<script type="module">
    import { createWsClient } from '/assets/js/ws.js';

    const list = document.querySelector('#calls');
    const initial = <?= json_encode($channelState) ?>;

    const render = (items) => {
        list.innerHTML = '';
        items.forEach((item) => {
            const card = document.createElement('div');
            card.className = 'bg-white shadow rounded p-4';
            card.innerHTML = `
                <div class="text-sm text-gray-500">Fila ${item.queueId}</div>
                <div class="text-3xl font-bold">${item.display}</div>
                <div class="text-sm text-gray-600">Status: ${item.status}</div>
            `;
            list.appendChild(card);
        });
    };

    render(initial);

    createWsClient({
        url: `ws://${location.hostname}:${location.port || 8080}?channel=<?= htmlspecialchars($channelId) ?>`,
        onMessage: (msg) => {
            if (msg.type && msg.payload) {
                render([msg.payload, ...initial].slice(0, 6));
            }
        }
    });
</script>
