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
    startingPriceLabel: { type: String, required: true },
    minIncrementLabel: { type: String, required: true },
    initialBidCount: { type: Number, required: true },
    initialIsOpen: { type: Boolean, required: true },
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
    <div class="tr-auction-panel">
        <section class="card tr-listing-detail__summary">
            <header class="tr-listing-detail__summary-heading">
                <h2>{{ state ? (state.is_open ? 'Subasta activa' : 'Subasta finalizada') : (initialIsOpen ? 'Subasta activa' : 'Subasta finalizada') }}</h2>
                <span>{{ state ? state.bid_count : initialBidCount }} ofertas</span>
            </header>
            <div class="card-body">
                <p v-if="error && !state" class="tr-auction-panel__error" role="alert">
                    No se pudo cargar el estado de la subasta. Actualizá la página para volver a intentar.
                </p>

                <template v-if="state">
                    <span class="tr-label">{{ state.has_bids ? 'Puja actual' : 'Puja inicial' }}</span>
                    <p class="tr-price tr-listing-detail__price">{{ state.current_price_label }}</p>

                    <div class="tr-listing-detail__auction-facts">
                        <div>
                            <span>Precio de salida</span>
                            <strong>{{ startingPriceLabel }}</strong>
                        </div>
                        <div>
                            <span>Incremento mínimo</span>
                            <strong>{{ minIncrementLabel }}</strong>
                        </div>
                    </div>

                    <p class="tr-auction-panel__countdown">
                        <template v-if="state.is_open">
                            Cierra en <AuctionCountdown :ends-at="state.ends_at" @finished="loadState" />
                        </template>
                        <template v-else>Subasta cerrada</template>
                    </p>

                    <div v-if="state.is_open" class="tr-auction-panel__form">
                        <div v-if="bidOk" class="alert alert-success" role="alert">{{ bidOk }}</div>

                        <p v-if="user && user.id === sellerId" class="tr-auction-panel__notice">
                            Esta es tu subasta. No podés pujar en ella.
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
                </template>
            </div>
        </section>

        <section class="card tr-listing-detail__bid-history">
            <header class="tr-listing-detail__bid-history-heading">
                <h2>Historial de pujas</h2>
                <span>{{ state ? state.bid_count : initialBidCount }} ofertas</span>
            </header>
            <div class="card-body">
                <p v-if="error && !state" class="tr-auction-panel__error" role="alert">
                    El historial no está disponible en este momento.
                </p>
                <BidList v-else-if="state" :bids="state.bids" />
                <p v-else class="tr-auction-panel__loading" aria-live="polite">Cargando pujas...</p>
            </div>
        </section>
    </div>
</template>