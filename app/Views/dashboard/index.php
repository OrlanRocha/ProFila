<section class="grid">
    <article class="card">
        <h2>Resumo diário</h2>
        <div class="summary">
            <div>
                <span class="summary__label">Em espera</span>
                <span class="summary__value"><?= (int) ($summary['waiting'] ?? 0) ?></span>
            </div>
            <div>
                <span class="summary__label">Chamadas</span>
                <span class="summary__value"><?= (int) ($summary['called'] ?? 0) ?></span>
            </div>
            <div>
                <span class="summary__label">Finalizadas</span>
                <span class="summary__value"><?= (int) ($summary['finished'] ?? 0) ?></span>
            </div>
        </div>
    </article>

    <article class="card">
        <h2>Últimas senhas</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Código</th>
                <th>Setor</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Gerada em</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($recentTickets)): ?>
                <tr>
                    <td colspan="5" class="table__empty">Nenhuma senha registrada.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentTickets as $ticket): ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket['code']) ?></td>
                        <td><?= htmlspecialchars($ticket['sector_name']) ?></td>
                        <td><?= htmlspecialchars($ticket['priority']) ?></td>
                        <td><?= htmlspecialchars($ticket['status']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ticket['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </article>
</section>
