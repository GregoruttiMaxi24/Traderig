<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { getMessages, sendMessage } from '../../services/chat.js';
import MessageList from './MessageList.vue';
import MessageForm from './MessageForm.vue';

const props = defineProps({

    messagesUrl: { type: String, required: true },
    sendUrl: { type: String, required: true },

});

//el usuario con sesión (lo inyecta el layout).
const user = window.TradeRig.user;

const messages = ref([]);
const loaded = ref(false);
const error = ref(false);
const sendError = ref('');
const sending = ref(false);
let timer = null;

// Pide los mensajes a Laravel y los guarda para que la pantalla se actualice.
async function loadMessages() {
    try {
        const data = await getMessages(props.messagesUrl);
        messages.value = data.messages;
        loaded.value = true;
        error.value = false;
    } catch (e) {
        error.value = true;
    }
}

// Envía el mensaje; si falla muestra el error, y siempre vuelve a pedir los mensajes.
async function send(text) {
    sending.value = true;
    sendError.value = '';

    try {
        await sendMessage(props.sendUrl, text);
    } catch (e) {
        sendError.value = e.message;
    } finally {
        sending.value = false;
    }

    await loadMessages();
}

onMounted(() => {
    loadMessages();
    timer = setInterval(loadMessages, 5000);
});

onUnmounted(() => clearInterval(timer));

</script>

<template>
    <div>
        <p v-if="error" class="text-danger mb-3" role="alert">
            No se pudo actualizar el chat. Vuelve a intentarlo solo.
        </p>

        <p v-if="!loaded && !error" class="text-body-secondary mb-0">El chat se está cargando...</p>

        <template v-if="loaded">
            <MessageList :messages="messages" :current-user-id="user.id" />
            <MessageForm :error="sendError" :sending="sending" @submit="send" />
        </template>
    </div>
</template>

<!-- <style>

</style> -->