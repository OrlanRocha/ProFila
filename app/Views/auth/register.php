<h2 class="text-2xl font-semibold mb-6">Crie sua conta</h2>
<form id="registerForm" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Nome</label>
        <input type="text" name="name" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">E-mail</label>
        <input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">CPF</label>
        <input type="text" name="cpf" class="mt-1 w-full border rounded px-3 py-2" placeholder="000.000.000-00">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Senha</label>
        <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded">Cadastrar</button>
    <p class="text-sm text-gray-600">Já tem conta? <a class="text-indigo-600" href="/login">Entre</a></p>
</form>

<script type="module">
    import { apiPost } from '/assets/js/api.js';

    const form = document.querySelector('#registerForm');
    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = Object.fromEntries(new FormData(form));
        try {
            const res = await apiPost('/api/auth/register', formData);
            if (res.ok) {
                toastr.success(res.msg || 'Conta criada! Faça login.');
                setTimeout(() => (window.location.href = '/login'), 400);
            } else {
                const details = res.errors ? Object.values(res.errors).join(' | ') : '';
                toastr.error(res.msg || details || 'Falha no cadastro');
            }
        } catch (err) {
            console.error(err);
            toastr.error('Erro de rede ao cadastrar');
        }
    });
</script>
