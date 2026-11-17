<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    endsAt: { type: String, required: true },
});

//  Avisar al contenedor cuando la cuenta llega a cero
const emit = defineEmits(['finished']); 

const now = ref(Date.now());
let timer = null;


const remaining = computed(() => new Date(props.endsAt) - now.value); // Milisegnudos que faltan para el cierre

//el texto: "2d 03h 10m" si falta más de un día o "05:40:12" si falta menos.
const text = computed(() => {
    const ms = remaining.value;
    if (ms <= 0) return 'Finalizada';

    const total = Math.floor(ms / 1000);
    const d = Math.floor(total / 86400);
    const h = Math.floor((total % 86400) / 3600);
    const m = Math.floor((total % 3600) / 60);
    const s = total % 60;
    const pad = (n) => String(n).padStart(2, '0');

    return d > 0 ? `${d}d ${pad(h)}h ${pad(m)}m` : `${pad(h)}:${pad(m)}:${pad(s)}`;
});

function tick() {
    now.value = Date.now();

    if (remaining.value <= 0) {
        clearInterval(timer);
        emit('finished');
    }
}

onMounted(() => {
    timer = setInterval(tick, 1000);
    tick();
});

onUnmounted(() => clearInterval(timer));


</script>


<template>

    <span>{{ text }}</span>
</template>