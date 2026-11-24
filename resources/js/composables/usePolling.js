import { onMounted, onUnmounted } from 'vue';

/**
 * Ejecuta una función enseguida y después la repite cada cierto tiempo.
 * 
 *Se frena sola cuando el componente desaparece de la pantalla
 *
 *
 */
export function usePolling(callback, interval) {
    
    let timer = null;

    onMounted(() => {
        callback();
        timer = setInterval(callback, interval);
    });

    onUnmounted(() => clearInterval(timer));
}