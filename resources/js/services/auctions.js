/**
 * Obtiene el estado de la subasta aislando la lógica de fetch de los componentes
 * (Así los componentes no conocen el fetch ni la forma de la respuesta)
 * 
 * @param {string} url - Endpoint de la API.
 */
export async function getAuctionState(url) {
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error('No se pudo leer el estado de la subasta');
    }

    return response.json();
}

/**
 * Envía una puja a Laravel. Si algo falla (monto bajo, subasta cerrada, etc.)tira un Error con el mensaje que devolvió el servidor.
 */
export async function placeBid(url, amount) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.TradeRig.csrfToken, // se pone por el @CSRF que está en el formulario
        },
        body: JSON.stringify({ amount }),
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(data.errors?.amount?.[0] ?? data.message ?? 'No se pudo registrar la puja.');
    }

    return data;
}