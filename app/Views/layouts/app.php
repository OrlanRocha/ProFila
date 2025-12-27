<?php use App\Services\MenuService; use App\Repositories\RoleRepository; use App\Core\Session; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset(
        $title
    ) ? htmlspecialchars($title) . ' — ProFila' : 'ProFila' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="/assets/js/api.js"></script>
    <script type="module" src="/assets/js/ws.js"></script>
    <script type="module" src="/assets/js/app.js"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
<?php
    $roleRepo = new RoleRepository();
    $menuService = new MenuService($roleRepo);
    $roleId = Session::get('user_role_id') ?: Session::get('user_role_id', null);
    $menu = $menuService->buildMenu($roleId ?? 0);
?>
    <header class="bg-indigo-700 text-white p-4 shadow flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-semibold">ProFila</span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm"><?= htmlspecialchars(Session::get('user_role') ?? 'Usuário') ?></span>
            <button data-logout class="text-sm bg-white/10 px-3 py-1 rounded border border-white/20">Sair</button>
        </div>
    </header>

    <div class="flex">
        <aside class="w-64 bg-white shadow-md min-h-screen p-4">
            <nav class="space-y-2">
                <?php foreach ($menu as $item): ?>
                    <a class="block px-3 py-2 rounded hover:bg-indigo-50" href="<?= $item['href'] ?>"><?= htmlspecialchars($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <main class="flex-1 p-6">
            <?= $yield ?? '' ?>
        </main>
    </div>
</body>
</html>
