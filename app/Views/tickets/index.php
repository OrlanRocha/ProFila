<section class="card">
    <h2>Gerenciar Atendimentos</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert--success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
            <ul>
                <?php foreach ((array) $errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/tickets" method="post" class="form form--inline">
        <div>
            <label for="sector_id">Setor</label>
            <select name="sector_id" id="sector_id" required>
                <option value="">Selecione</option>
                <?php foreach ($sectors as $sector): ?>
                    <option value="<?= (int) $sector['id'] ?>" <?= $selectedSector === (int) $sector['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sector['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="priority">Prioridade</label>
            <select name="priority" id="priority" required>
                <?php foreach ($priorities as $priority): ?>
                    <option value="<?= htmlspecialchars($priority) ?>"><?= ucfirst($priority) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn">Gerar senha</button>
    </form>

    <form action="/tickets/call" method="post" class="form form--inline">
        <div>
            <label for="sector_call">Setor</label>
            <select name="sector_id" id="sector_call" required>
                <option value="">Selecione</option>
                <?php foreach ($sectors as $sector): ?>
                    <option value="<?= (int) $sector['id'] ?>" <?= $selectedSector === (int) $sector['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sector['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="counter_id">Guichê</label>
            <select name="counter_id" id="counter_id" required>
                <option value="">Selecione</option>
                <?php foreach ($counters as $counter): ?>
                    <option value="<?= (int) $counter['id'] ?>"><?= htmlspecialchars($counter['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn--secondary">Chamar próxima senha</button>
    </form>
</section>

<section class="card">
    <h2>Senhas recentes</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Código</th>
            <th>Setor</th>
            <th>Prioridade</th>
            <th>Status</th>
            <th>Guichê</th>
            <th>Gerada em</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="7" class="table__empty">Nenhuma senha encontrada.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?= htmlspecialchars($ticket['code']) ?></td>
                    <td><?= htmlspecialchars($ticket['sector_name']) ?></td>
                    <td><?= htmlspecialchars($ticket['priority']) ?></td>
                    <td><?= htmlspecialchars($ticket['status']) ?></td>
                    <td><?= htmlspecialchars($ticket['counter_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ticket['created_at']))) ?></td>
                    <td>
                        <?php if ($ticket['status'] !== 'finished'): ?>
                            <form action="/tickets/finish" method="post">
                                <input type="hidden" name="ticket_id" value="<?= (int) $ticket['id'] ?>">
                                <button type="submit" class="btn btn--link">Finalizar</button>
                            </form>
                        <?php else: ?>
                            <span class="badge">Concluído</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>
