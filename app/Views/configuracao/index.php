<section class="space-y-6">
    <h2 class="text-2xl font-semibold">Configurações</h2>
    <p class="text-gray-600">Monitores, canais, pontos e downloads.</p>

    <div class="bg-white shadow rounded p-4 space-y-3">
        <h3 class="text-lg font-semibold mb-3">Monitores registrados</h3>
        <?php if (empty($monitors)): ?>
            <p class="text-gray-600">Nenhum monitor registrado ainda.</p>
        <?php else: ?>
            <ul class="space-y-2">
                <?php foreach ($monitors as $monitor): ?>
                    <li class="border rounded p-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium">Canal <?= htmlspecialchars($monitor->channelId) ?></p>
                            <p class="text-sm text-gray-500">IP: <?= htmlspecialchars($monitor->ip) ?></p>
                        </div>
                        <span class="text-xs <?= $monitor->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> px-2 py-1 rounded">
                            <?= $monitor->active ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white shadow rounded p-4">
            <h4 class="text-lg font-semibold mb-2">Layout de monitores</h4>
            <p class="text-gray-600 text-sm">Cores, cabeçalho, informativo, mídia opcional.</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h4 class="text-lg font-semibold mb-2">Downloads</h4>
            <p class="text-gray-600 text-sm">Catálogo de agentes e programas (ativo/homolog/arquivado/legado).</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h4 class="text-lg font-semibold mb-2">Canais</h4>
            <p class="text-gray-600 text-sm">Nome, estado, linhas (1–3), intervalo e exigência de monitores conectados.</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h4 class="text-lg font-semibold mb-2">Pontos</h4>
            <p class="text-gray-600 text-sm">Ponto de recepção e atendimento: estado, prioridade e UO.</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h4 class="text-lg font-semibold mb-2">Usuários x UO</h4>
            <p class="text-gray-600 text-sm">Atribuição de perfis e múltiplas unidades organizacionais.</p>
        </div>
    </div>
</section>
