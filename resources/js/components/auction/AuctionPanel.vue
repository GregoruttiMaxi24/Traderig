<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { getAuctionState, placeBid } from '../../services/auctions.js';
import AuctionCountdown from './AuctionCountdown.vue';
import BidForm from './BidForm.vue';
import BidList from './BidList.vue';

const props = defineProps({
    stateUrl: { type: String, required: true },
    bidUrl: { type: String, required: true },
    loginUrl: { type: String, required: true },
    sellerId: { type: Number, required: true },
});

// Usuario con sesión (lo inyecta el layout) o null si es un visitante.
const user = window.TradeRig.user;

const state = ref(null);
const error = ref(false);
const bidError = ref('');
const bidOk = ref('');
const sending = ref(false);
let timer = null;

//pide el estado a Laravel y lo guarda para que la pantalla se actualice
async function loadState() {
    try {
        state.value = await getAuctionState(props.stateUrl);
        error.value = false;
    } catch (e) {
        error.value = true;
    }
}

//se encarga de enviar la puja; muestra el error o el aviso de éxito y refresca el estado.
async function sendBid(amount) {
    sending.value = true;
    bidError.value = '';
    bidOk.value = '';

    try {
        const result = await placeBid(props.bidUrl, amount);
        bidOk.value = result.message;
        setTimeout(() => (bidOk.value = ''), 6000);
    } catch (e) {
        bidError.value = e.message;
    } finally {
        sending.value = false;
    }

    await loadState();
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

            <div v-if="state.is_open" class="mt-4">
                <div v-if="bidOk" class="alert alert-success" role="alert">{{ bidOk }}</div>

                <p v-if="user && user.id === sellerId" class="tr-meta text-body-secondary mb-0">
                    Esta es tu subasta: no podés pujar en ella.
                </p>
                <a v-else-if="!user" :href="loginUrl" class="btn btn-primary btn-lg w-100">Ingresá para pujar</a>
                <BidForm
                    v-else
                    :minimum="state.minimum_next_bid"
                    :minimum-label="state.minimum_next_bid_label"
                    :error="bidError"
                    :sending="sending"
                    @submit="sendBid"
                />
            </div>

            <h2 class="tr-subtitle mt-4 mb-3">Últimas pujas</h2>
            <BidList :bids="state.bids" />

        </template>
    </div>

    
</template>