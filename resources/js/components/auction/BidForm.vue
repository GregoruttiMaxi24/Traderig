<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    minimum: { type: Number, required: true },
    minimumLabel: { type: String, required: true },
    error: { type: String, default: '' },
    sending: { type: Boolean, default: false },
});

// Avisa (emite el evento ) al contenedor con el monto $ que el usuario quiere pujar hacia el componente padre
const emit = defineEmits(['submit']);

const amount = ref(props.minimum);


//si alguien puja y el mínimo sube, el campo se actualiza para no quedar desactualizado.
watch(() => props.minimum, (newMinimum) => {
    if (amount.value < newMinimum) {
        amount.value = newMinimum;
    }
});

</script>


<template>
    <form class="tr-bid-box" novalidate @submit.prevent="emit('submit', amount)">
        <label for="amount" class="tr-bid-title">Hacé tu puja</label>

        <div class="input-group input-group-lg has-validation">
            <span class="input-group-text">$</span>
            <input
                v-model.number="amount"
                type="number"
                id="amount"
                name="amount"
                class="form-control"
                :class="{ 'is-invalid': error }"
                :min="minimum"
                max="6000000"
                step="1"
                required
            >
            <div v-if="error" class="invalid-feedback">{{ error }}</div>
        </div>

        <p class="tr-bid-hint">Puja mínima: <span class="tr-price">{{ minimumLabel }}</span></p>

        <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="sending">
            {{ sending ? 'Enviando...' : 'Pujar' }}
        </button>
    </form>

</template>