document.addEventListener('DOMContentLoaded', () => {
    const sectorSelect = document.querySelector('#sector_call');
    const counterSelect = document.querySelector('#counter_id');

    if (sectorSelect && counterSelect && counterSelect.options.length === 1) {
        counterSelect.disabled = true;
    }

    sectorSelect?.addEventListener('change', () => {
        const selectedSector = sectorSelect.value;
        if (!selectedSector) {
            counterSelect.innerHTML = '<option value="">Selecione</option>';
            counterSelect.disabled = true;
            return;
        }

        fetch(`/api/counters?sector=${encodeURIComponent(selectedSector)}`)
            .then(response => response.ok ? response.json() : [])
            .then(data => {
                counterSelect.innerHTML = '<option value="">Selecione</option>';
                data.forEach(counter => {
                    const option = document.createElement('option');
                    option.value = counter.id;
                    option.textContent = counter.name;
                    counterSelect.appendChild(option);
                });
                counterSelect.disabled = data.length === 0;
            })
            .catch(() => {
                counterSelect.disabled = true;
            });
    });
});
