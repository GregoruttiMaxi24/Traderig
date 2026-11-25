
<script setup>

    const props = defineProps({
        messages: { type: Array, required: true },
        currentUserId: { type: Number, required: true },
});

// Convierte la fecha que manda Laravel (en UTC) a la hora del navegador de cada persona.
function formatTime(isoDate) {
    return new Date(isoDate).toLocaleString('es-AR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
}

</script>

<template>
    <div class="tr-chat-messages">
        <p v-if="messages.length === 0" class="text-body-secondary mb-0">
            Todavía no hay mensajes. ¡Escribí el primero!
        </p>

        <div
            v-for="message in messages"
            :key="message.id"
            class="tr-message"
            :class="message.sender_id === currentUserId ? 'tr-message-own' : 'tr-message-other'"
        >
            <p class="tr-message-text">{{ message.message }}</p>
            <span class="tr-meta tr-message-meta">{{ message.sender_name }} · {{ formatTime(message.created_at) }}</span>
        </div>
    </div>
</template>

<style> 
</style>