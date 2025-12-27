<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Indicadores</h2>
            <p class="text-gray-600">TMA, TME e visão em tempo real.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">TMA (Tempo Médio de Atendimento)</p>
            <p class="text-3xl font-bold"><?= htmlspecialchars($indicators['tma']['avg_service_time'] ?? 0) ?>s</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">TME (Tempo Médio de Espera)</p>
            <p class="text-3xl font-bold"><?= htmlspecialchars($indicators['tme']['avg_wait_time'] ?? 0) ?>s</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Filas em operação</p>
            <p class="text-3xl font-bold"><?= htmlspecialchars($indicators['realtime']['waiting'] ?? 0) ?></p>
        </div>
    </div>
</section>
