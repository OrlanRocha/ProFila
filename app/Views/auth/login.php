<section class="card card--centered">
    <h2>Acesso restrito</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert--info"><?= htmlspecialchars($message) ?></div>
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
    <form action="/login" method="post" class="form">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn">Entrar</button>
    </form>
</section>
