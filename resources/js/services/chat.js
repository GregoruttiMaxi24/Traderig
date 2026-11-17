/**
 * Pide a Laravel los mensajes de una conversación.
 * 
 * Así los componentes no conocen el fetch ni la forma de la respuesta.
 *
 */
export async function getMessages(url) { //url es la dirección de los mensajes de la conversación
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
    });

        if (!response.ok) {
            throw new Error('No se pudieron leer los mensajes');
        }

    return response.json();
}

/**
 * Envía un mensaje nuevo a Laravel.
 * Si algo falla (mensaje vacío, demasiado largo, etc.) tira un error con el texto que devolvió el servidor.
 *
 */
export async function sendMessage(url, message) { //mensage: es el texto que escribió el usuario
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.TradeRig.csrfToken, //es porque Laravel exige ese token en los envíos POST
        },
        body: JSON.stringify({ message }),
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        if (data.errors && data.errors.message) {
            throw new Error(data.errors.message[0]);
        }

        throw new Error('No se pudo enviar el mensaje.');
    }

    return data;
}