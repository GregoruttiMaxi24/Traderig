<script setup>
import BaseButton from './BaseButton.vue';
import BaseLoader from './BaseLoader.vue';

defineProps({
    loading: { type: Boolean, default: false },
});
</script>

<template>
    <BaseButton :disabled="loading">

        <slot v-if="!loading" /> <!-- si no carga mostrar el mensaje de cargando-->

         <BaseLoader v-else /> <!--mostrar el mensaje de cargando -->

    </BaseButton>
</template>