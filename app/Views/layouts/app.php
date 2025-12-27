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
    <header class="bg-indigo-700 text-white p-4 shadow">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <h1 class="text-xl font-semibold">ProFila</h1>
            <nav class="space-x-4">
                <a class="hover:underline" href="/atendimento">Atendimento</a>
                <a class="hover:underline" href="/dashboards">Dashboards</a>
                <a class="hover:underline" href="/cadastro">Cadastros</a>
                <a class="hover:underline" href="/config">Configuração</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto py-8 px-4">
        <?= $yield ?? '' ?>
    </main>
</body>
</html>
