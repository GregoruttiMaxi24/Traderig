// TRADERIG - JavaScript propio (se carga en todas las páginas).
window.TradeRig = {
    // Token CSRF para los fetch() de las próximas fases (pujas, polling).
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content ?? '',
};

// Los mensajes de éxito se cierran solos a los 6 segundos.
document.querySelectorAll('[data-auto-dismiss]').forEach((alert) => {
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(alert).close(), 6000);
});

// Cuenta regresiva: <span data-countdown="2026-10-25T18:00:00-03:00"></span>
function formatRemaining(ms) {
    if (ms <= 0) return 'Finalizada';
    const total = Math.floor(ms / 1000);
    const d = Math.floor(total / 86400);
    const h = Math.floor((total % 86400) / 3600);
    const m = Math.floor((total % 3600) / 60);
    const s = total % 60;
    const pad = (n) => String(n).padStart(2, '0');
    return d > 0 ? `${d}d ${pad(h)}h ${pad(m)}m` : `${pad(h)}:${pad(m)}:${pad(s)}`;
}

function tickCountdowns() {
    document.querySelectorAll('[data-countdown]').forEach((el) => {
        el.textContent = formatRemaining(new Date(el.dataset.countdown) - Date.now());
    });
}

tickCountdowns();
setInterval(tickCountdowns, 1000);

// Botón "Copiar enlace"
document.querySelectorAll('[data-copy-link]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(window.location.href);
            const original = button.textContent;
            button.textContent = 'Enlace copiado';
            setTimeout(() => (button.textContent = original), 2000);
        } catch (e) {
            // Si el navegador no permite copiar, no hacemos nada.
        }
    });
});
