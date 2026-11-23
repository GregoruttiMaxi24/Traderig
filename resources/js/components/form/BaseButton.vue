<script setup>

import { computed } from 'vue';



const props = defineProps({
    variant: {
         type: String, 
         default: 'primary' },
});

//Cada variante tiene sus clases completas sin tener que armarlas concatenando textos.
const VARIANT_CLASSES = {
    primary: 'btn btn-primary',
    secondary: 'btn btn-outline-tr',
};

const variantClasses = computed(() => VARIANT_CLASSES[props.variant]);


</script>

<template>
    
    <button type="submit" :class="variantClasses">
        <slot>Enviar</slot>
    </button>
</template>