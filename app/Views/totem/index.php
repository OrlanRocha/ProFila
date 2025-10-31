<?php
/** @var array $servicos */
/** @var array $categorias */
/** @var array $filas */
/** @var callable $url */
/** @var string $token */
?>
<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Totem de Atendimento - ProFila</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= htmlspecialchars(($config['app']['base_url'] ?? '') . '/public/assets/css/app.css', ENT_QUOTES) ?>">
    <style>
        body { background: radial-gradient(circle at top, #0ea5e9, #020617 60%); min-height: 100vh; font-family: 'Inter', sans-serif; }
        .totem-shell { max-width: 960px; margin: 0 auto; padding: 3rem 1.5rem; }
        .totem-card { border-radius: 24px; backdrop-filter: blur(16px); background: rgba(15, 23, 42, 0.8); box-shadow: 0 40px 120px rgba(2, 6, 23, 0.55); }
        .option-card { border-radius: 20px; background: rgba(148, 163, 184, 0.15); transition: transform .3s ease, background .3s ease; min-height: 220px; }
        .option-card:hover { transform: translateY(-6px); background: rgba(14, 165, 233, 0.25); }
    </style>
</head>
<body>
<div class="totem-shell">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between text-white mb-5">
        <div>
            <h1 class="display-5 fw-bold">Bem-vindo ao ProFila</h1>
            <p class="lead text-secondary">Escolha entre agendar um atendimento ou emitir uma senha imediata com o perfil adequado.</p>
        </div>
        <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4bb.svg" alt="Ícone totem" width="96" height="96" class="d-none d-lg-block">
    </div>
    <div class="totem-card p-4 p-lg-5 text-white">
        <?php if (!empty($flash)): ?>
            <div class="alert alert-success bg-opacity-25 border-info text-info mb-4"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="option-card h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-stack bg-info bg-opacity-25 text-info rounded-circle p-3 me-3">
                            <i class="bi bi-calendar-check fs-3"></i>
                        </div>
                        <div>
                            <h2 class="h4 mb-0">Agendar atendimento</h2>
                            <small class="text-secondary">Garanta horário e priorização</small>
                        </div>
                    </div>
                    <form method="post" action="<?= htmlspecialchars($url('totem/agendar'), ENT_QUOTES) ?>" class="d-flex flex-column gap-3">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                        <input type="text" class="form-control form-control-lg" name="nome" placeholder="Nome completo" required>
                        <div class="row g-3">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-lg" name="data_agendada" required>
                            </div>
                            <div class="col-6">
                                <input type="time" class="form-control form-control-lg" name="hora_agendada" required>
                            </div>
                        </div>
                        <select class="form-select form-select-lg" name="servico_id" required>
                            <option value="">Selecione o serviço</option>
                            <?php foreach ($servicos as $servico): ?>
                                <option value="<?= htmlspecialchars($servico['id']) ?>"><?= htmlspecialchars($servico['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-select form-select-lg" name="categoria">
                            <option value="">Categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria['nome']) ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-select form-select-lg" name="prioridade_tipo">
                            <option value="padrao">Atendimento padrão</option>
                            <option value="preferencial">Preferencial</option>
                            <option value="80+">80+ anos</option>
                            <option value="servico">Serviço especializado</option>
                        </select>
                        <textarea class="form-control form-control-lg" rows="2" name="observacoes" placeholder="Observações"></textarea>
                        <button type="submit" class="btn btn-info btn-lg mt-2">Confirmar agendamento</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="option-card h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-stack bg-success bg-opacity-25 text-success rounded-circle p-3 me-3">
                            <i class="bi bi-lightning-charge fs-3"></i>
                        </div>
                        <div>
                            <h2 class="h4 mb-0">Atendimento imediato</h2>
                            <small class="text-secondary">Receba sua senha agora</small>
                        </div>
                    </div>
                    <form method="post" action="<?= htmlspecialchars($url('totem/atendimento-imediato'), ENT_QUOTES) ?>" class="d-flex flex-column gap-3">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                        <select class="form-select form-select-lg" name="fila_id" required>
                            <option value="">Escolha o serviço</option>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= htmlspecialchars($fila['id']) ?>">[<?= htmlspecialchars($fila['sigla']) ?>] <?= htmlspecialchars($fila['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-select form-select-lg" name="prioridade_tipo">
                            <option value="padrao">Padrão</option>
                            <option value="preferencial">Preferencial</option>
                            <option value="80+">80+</option>
                            <option value="servico">Serviço</option>
                        </select>
                        <button type="submit" class="btn btn-success btn-lg mt-2">Emitir senha</button>
                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info mb-0"><?= htmlspecialchars($flash) ?></div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
