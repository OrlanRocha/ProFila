<section class="space-y-6">
    <h2 class="text-2xl font-semibold">Sala de Situação</h2>
    <p class="text-gray-600">Acompanhe filas em andamento e status dos pontos.</p>

    <?php foreach ($liveQueues as $queueId => $tickets): ?>
        <div class="bg-white shadow rounded p-4 mb-4">
            <h3 class="text-lg font-semibold">Fila <?= htmlspecialchars((string) $queueId) ?></h3>
            <table class="min-w-full mt-2" id="tbl-<?= $queueId ?>">
                <thead><tr><th class="text-left">Senha</th><th class="text-left">Status</th><th class="text-left">Ponto</th></tr></thead>
                <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td class="py-1"><?= htmlspecialchars($ticket->display) ?></td>
                        <td class="py-1"><?= htmlspecialchars($ticket->status) ?></td>
                        <td class="py-1"><?= htmlspecialchars((string) ($ticket->currentPointId ?? '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</section>

<script>
    document.querySelectorAll('table[id^="tbl-"]').forEach((table) => {
        if (window.jQuery && jQuery.fn.DataTable) {
            jQuery(table).DataTable({ pageLength: 10 });
        }
    });
</script>
