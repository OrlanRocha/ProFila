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

            const dataTableDefaults = {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                },
                dom: 'Bfrtip',
                buttons: ['csv', 'print']
            };

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
                            form.action = form.dataset.delete;
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
                            form.action = form.dataset.reset;
                            form.querySelector('input[name="id"]').value = id;
                            form.submit();
                        }
                    });
                });
            }

            // Listagens de filas e guichês possuem scripts dedicados nas próprias páginas.
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

            if ($('#servicosTable').length) {
                $('#servicosTable').DataTable({
                    ...dataTableDefaults,
                    order: [[1, 'asc']]
                });
            }

            if ($('#agendamentosTable').length) {
                $('#agendamentosTable').DataTable({
                    ...dataTableDefaults,
                    order: [[3, 'desc']],
                    columnDefs: [
                        { targets: [3], type: 'date-eu' }
                    ]
                });
            }
        }

        // Fluxos de operação ajustados diretamente nos módulos.
    });
})();
