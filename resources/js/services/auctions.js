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