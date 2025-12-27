<section class="space-y-6">
    <h2 class="text-2xl font-semibold">Cadastros</h2>
    <p class="text-gray-600">Gerencie filas, serviços e usuários.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded p-4">
            <h3 class="text-lg font-semibold mb-3">Filas</h3>
            <ul class="space-y-2">
                <?php foreach ($queues as $queue): ?>
                    <li class="border rounded p-3">
                        <p class="font-medium"><?= htmlspecialchars($queue->name) ?></p>
                        <p class="text-sm text-gray-500">Letra: <?= htmlspecialchars($queue->letter) ?> · Política: <?= htmlspecialchars($queue->policy) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="bg-white shadow rounded p-4">
            <h3 class="text-lg font-semibold mb-3">Usuários</h3>
            <ul class="space-y-2">
                <?php foreach ($users as $user): ?>
                    <li class="border rounded p-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($user->name) ?></p>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($user->role) ?></p>
                        </div>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($user->email) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
