(function () {
    const state = window.ProFila?.painel || {};
    const codigo = document.getElementById('painelCodigo');
    const guiche = document.getElementById('painelGuiche');
    const fila = document.getElementById('painelFila');
    const historico = document.getElementById('painelHistorico');
    let audioCtx;
    let resumeBound = false;

    const ensureAudioContext = () => {
        if (audioCtx) {
            return audioCtx;
        }
        const Context = window.AudioContext || window.webkitAudioContext;
        if (!Context) {
            return null;
        }
        audioCtx = new Context();
        return audioCtx;
    };

    const resumeOnInteraction = () => {
        if (resumeBound || !ensureAudioContext()) {
            return;
        }
        resumeBound = true;
        const resume = () => {
            if (audioCtx?.state === 'suspended') {
                audioCtx.resume().catch(() => {});
            }
        };
        ['click', 'touchstart', 'keydown'].forEach((eventName) => {
            document.addEventListener(eventName, resume, { once: true, passive: true });
        });
    };

    const playNotification = () => {
        const ctx = ensureAudioContext();
        if (!ctx) {
            return;
        }
        const oscillator = ctx.createOscillator();
        const gain = ctx.createGain();
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, ctx.currentTime);
        gain.gain.setValueAtTime(0.0001, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.4, ctx.currentTime + 0.05);
        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.6);
        oscillator.connect(gain);
        gain.connect(ctx.destination);
        oscillator.start();
        oscillator.stop(ctx.currentTime + 0.6);
    };
    const hora = document.getElementById('painelHora');

    const updateClock = () => {
        if (hora) {
            const now = new Date();
            hora.textContent = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }
    };

    const renderHistorico = (items) => {
        if (!historico) {
            return;
        }
        historico.innerHTML = '';
        items.slice(0, 3).forEach((item) => {
            const col = document.createElement('div');
            col.className = 'col-md-4';
            col.innerHTML = `
                <div class="card bg-transparent border-secondary text-white text-center">
                    <div class="card-body p-3">
                        <div class="fw-bold fs-4">${item.codigo}</div>
                        <div class="text-secondary">Guichê ${item.guiche}</div>
                    </div>
                </div>`;
            historico.appendChild(col);
        });
    };

    const applyData = (data) => {
        if (!data) {
            return;
        }
        codigo.textContent = data.codigo || '--';
        guiche.textContent = data.guiche || '--';
        fila.textContent = data.fila || '--';
        renderHistorico([data, ...(state.history || [])]);
        state.history = [data, ...(state.history || [])].slice(0, 3);
        playNotification();
    };

    const fetchFallback = () => {
        fetch(state.fallback, { cache: 'no-store' })
            .then(response => response.json())
            .then(json => {
                if (json?.data && json.data.codigo && (!state.last || state.last.codigo !== json.data.codigo)) {
                    state.last = json.data;
                    applyData(json.data);
                }
            })
            .catch(console.error);
    };

    const startSse = () => {
        const source = new EventSource(state.endpoint);
        source.addEventListener('senha', (event) => {
            try {
                const data = JSON.parse(event.data);
                state.last = data;
                applyData(data);
            } catch (error) {
                console.error(error);
            }
        });
        source.onerror = () => {
            source.close();
            state.sse = false;
            setInterval(fetchFallback, state.pollInterval || 5000);
        };
    };

    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 60000);

        if (state.history?.length) {
            renderHistorico(state.history);
        }

        resumeOnInteraction();

        if (state.sse && window.EventSource) {
            startSse();
        } else {
            fetchFallback();
            setInterval(fetchFallback, state.pollInterval || 5000);
        }
    });
})();
