<h2 class="text-2xl font-semibold mb-6">Acesse o ProFila</h2>
<form id="loginForm" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">E-mail</label>
        <input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Senha</label>
        <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded">Entrar</button>
    <p class="text-sm text-gray-600 text-center">Novo aqui? <a class="text-indigo-600" href="/register">Crie sua conta</a></p>
    <p class="text-xs text-gray-500 text-center mt-2"><a class="underline" href="/install">Instalar</a></p>
</form>
<script type="module">
    import { apiPost } from '/assets/js/api.js';

    const form = document.querySelector('#loginForm');
    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = Object.fromEntries(new FormData(form));
        try {
            const res = await apiPost('/api/auth/login', formData);
            if (res.ok) {
                toastr.success(res.msg || 'Login realizado');
                setTimeout(() => (window.location.href = '/dashboards'), 300);
            } else {
                const details = res.errors ? Object.values(res.errors).join(' | ') : '';
                toastr.error(res.msg || details || 'Falha no login');
            }
        } catch (err) {
            console.error(err);
            toastr.error('Erro de rede ao autenticar');
        }
    });
</script>
