<script setup>
import { ref, watch } from 'vue';
import LoaderButton from '../form/LoaderButton.vue';

const props = defineProps({
    error: { 
        type: String, 
        default: '' },
    sending: { 
        type: Boolean, 
        default: false },
});

//emitir el evento al contenedor con el texto que el usuario quiere enviar + validación en consola p el desarrollador (no se rompe nada). Laravel hace la validación real.
const emit = defineEmits({
    submit(text) {
        return typeof text === 'string';
    },
});

const text = ref('');

//Cuando termina de enviar sin error, vaciar la caja de mensajes para escribir el proximo mensaje
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
               <LoaderButton :loading="sending">Enviar</LoaderButton>

            <div v-if="error" class="invalid-feedback">{{ error }}</div>
        </div>
        
    </form>
</template>