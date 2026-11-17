<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    error: { type: String, default: '' },
    sending: { type: Boolean, default: false },
});

//emite el evento al contenedor con el texto que el usuario quiere enviar
const emit = defineEmits(['submit']);

const text = ref('');

//Cuando termina de enviar sin error, vaciar la caja para escribir el siguiente mensaje
watch(() => props.sending, (isSending, wasSending) => {

    if (wasSending && !isSending && props.error === '') {
        text.value = '';
    }

});

</script>

<template>

    <form class="tr-chat-form" novalidate @submit.prevent="emit('submit', text)">
       
        <div class="input-group has-validation">
            <input
                v-model="text"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': error }"
                placeholder="Escribí tu mensaje"
                maxlength="1000"
                aria-label="Mensaje"
            >
            <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Enviando...' : 'Enviar' }}
            </button>
            <div v-if="error" class="invalid-feedback">{{ error }}</div>
        </div>
        
    </form>
</template>