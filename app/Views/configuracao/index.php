<section class="space-y-6">
    <h2 class="text-2xl font-semibold">Configurações</h2>
    <p class="text-gray-600">Monitores, canais e layout do painel.</p>

    <div class="bg-white shadow rounded p-4">
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
</section>
