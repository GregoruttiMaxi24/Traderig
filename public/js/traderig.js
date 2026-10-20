// TRADERIG - JavaScript propio (se carga en todas las páginas).
window.TradeRig = {
    // Token CSRF para los fetch() de las próximas fases (pujas, polling).
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content ?? '',
};

// Los mensajes de éxito se cierran solos a los 6 segundos.
document.querySelectorAll('[data-auto-dismiss]').forEach((alert) => {
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(alert).close(), 6000);
});
