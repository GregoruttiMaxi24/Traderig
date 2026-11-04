<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { getAuctionState } from '../../services/auctions.js';
import AuctionCountdown from './AuctionCountdown.vue';
import BidList from './BidList.vue';

const props = defineProps({
    stateUrl: { type: String, required: true },
});

const state = ref(null);
const error = ref(false);
let timer = null;

/**
 * Obtiene el estado de la subasta y actualiza las variables del componente.
 * 
 *  Si sale todo ok: Guarda los datos en `state.value` y limpia el error.
 *  Si falla: activa `error.value`
 *
 */
async function loadState() {
    try {
        state.value = await getAuctionState(props.stateUrl);
        error.value = false;
    } catch (e) {
        error.value = true;
    }
}

onMounted(() => {
    loadState();
    timer = setInterval(loadState, 5000);
});

onUnmounted(() => clearInterval(timer));
</script>


<template>

 <div>
        <p v-if="error && !state" class="text-body-secondary mb-0">
            No se pudo cargar el estado de la subasta.
        </p>

        <template v-if="state">
            <span class="tr-label">{{ state.has_bids ? 'Puja actual' : 'Puja inicial' }}</span>
            <p class="tr-price display-6 mb-1">{{ state.current_price_label }}</p>

            <p class="tr-meta mb-0">
                <template v-if="state.is_open">
                    Cierra en <AuctionCountdown :ends-at="state.ends_at" @finished="loadState" />
                </template>
                <template v-else>Subasta cerrada</template>
            </p>

            <h2 class="tr-subtitle mt-5 mb-2">Últimas pujas:</h2>
            <BidList :bids="state.bids" />
         </template>
    </div>
</template>