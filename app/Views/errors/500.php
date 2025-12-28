<div class="text-center space-y-4">
    <p class="text-8xl font-bold text-red-600">500</p>
    <h1 class="text-2xl font-semibold">Erro interno</h1>
    <p class="text-gray-600"><?= htmlspecialchars($message ?? 'Algo inesperado aconteceu ao processar sua solicitação.') ?></p>
    <div class="flex items-center justify-center space-x-3">
        <button onclick="location.reload()" class="bg-red-600 text-white px-4 py-2 rounded">Tentar novamente</button>
        <a href="/" class="px-4 py-2 rounded border">Voltar ao início</a>
    </div>
</div>
