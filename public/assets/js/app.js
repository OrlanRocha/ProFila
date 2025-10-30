(function () {
    const toast = (message, type = 'info') => {
        Toastify({
            text: message,
            duration: 3500,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: type === 'success' ? '#198754' : type === 'error' ? '#dc3545' : '#0d6efd'
        }).showToast();
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (window.jQuery) {
            const $ = window.jQuery;

            if ($('#usuariosTable').length) {
                $('#usuariosTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                    },
                    dom: 'Bfrtip',
                    buttons: ['csv', 'print'],
                });

                $('#usuariosTable').on('click', 'button[data-action="delete"]', function () {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Remover usuário?',
                        text: 'Esta ação não pode ser desfeita.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, remover',
                        cancelButtonText: 'Cancelar'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('usuarioActions');
                            form.action = 'index.php?r=usuarios/delete';
                            form.querySelector('input[name="id"]').value = id;
                            form.submit();
                        }
                    });
                });

                $('#usuariosTable').on('click', 'button[data-action="reset"]', function () {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Resetar senha?',
                        text: 'Uma nova senha aleatória será gerada.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, resetar',
                        cancelButtonText: 'Cancelar'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('usuarioActions');
                            form.action = 'index.php?r=usuarios/reset';
                            form.querySelector('input[name="id"]').value = id;
                            form.submit();
                        }
                    });
                });
            }

            if ($('#filasTable').length) {
                $('#filasTable').DataTable({
                    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
                    dom: 'Bfrtip',
                    buttons: ['csv', 'print']
                });

                $('#filasTable').on('click', 'button[data-action="edit"]', function () {
                    const data = $(this).closest('tr').data('fila');
                    const modal = document.getElementById('filaModal');
                    modal.querySelector('#filaId').value = data.id;
                    modal.querySelector('#filaNome').value = data.nome;
                    modal.querySelector('#filaSigla').value = data.sigla;
                    modal.querySelector('#filaPrioridade').value = data.prioridade_padrao;
                    modal.querySelector('#filaAtivo').checked = !!Number(data.ativo);
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                    modalInstance.show();
                });

                $('#filasTable').on('click', 'button[data-action="delete"]', function () {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Remover fila?',
                        text: 'Confirme para continuar.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Remover',
                        cancelButtonText: 'Cancelar'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('filaDelete');
                            form.querySelector('input[name="id"]').value = id;
                            form.submit();
                        }
                    });
                });
            }

            if ($('#guichesTable').length) {
                $('#guichesTable').DataTable({
                    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
                    dom: 'Bfrtip',
                    buttons: ['csv', 'print']
                });

                $('#guichesTable').on('click', 'button[data-action="edit"]', function () {
                    const data = $(this).closest('tr').data('guiche');
                    const modal = document.getElementById('guicheModal');
                    modal.querySelector('#guicheId').value = data.id;
                    modal.querySelector('#guicheNumero').value = data.numero;
                    modal.querySelector('#guicheApelido').value = data.apelido || '';
                    modal.querySelector('#guicheFila').value = data.fila_padrao_id || '';
                    modal.querySelector('#guicheAtivo').checked = !!Number(data.ativo);
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });

                $('#guichesTable').on('click', 'button[data-action="delete"]', function () {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Remover guichê?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Remover',
                        cancelButtonText: 'Cancelar'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('guicheDelete');
                            form.querySelector('input[name="id"]').value = id;
                            form.submit();
                        }
                    });
                });
            }
        }

        if (window.ProFila && window.ProFila.dashboard) {
            const ctxFila = document.getElementById('graficoFila');
            const ctxDia = document.getElementById('graficoDia');
            if (ctxFila && window.Chart) {
                const data = window.ProFila.dashboard.porFila || [];
                new Chart(ctxFila, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.nome),
                        datasets: [{
                            label: 'Senhas',
                            data: data.map(item => Number(item.total)),
                            backgroundColor: '#0d6efd'
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
            if (ctxDia && window.Chart) {
                const data = window.ProFila.dashboard.porDia || [];
                new Chart(ctxDia, {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.dia),
                        datasets: [{
                            label: 'Senhas',
                            data: data.map(item => Number(item.total)),
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25,135,84,0.2)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
        }

        const formProxima = document.getElementById('formProxima');
        const formRechamar = document.getElementById('formRechamar');
        if (formProxima) {
            formProxima.addEventListener('submit', (event) => {
                const guiche = document.getElementById('guicheSelect').value;
                formProxima.action = 'index.php?r=senhas/proxima&guiche=' + encodeURIComponent(guiche);
            });
        }
        if (formRechamar) {
            formRechamar.addEventListener('submit', (event) => {
                const guiche = document.getElementById('guicheSelect').value;
                formRechamar.action = 'index.php?r=senhas/rechamar&guiche=' + encodeURIComponent(guiche);
            });
        }
    });
})();
