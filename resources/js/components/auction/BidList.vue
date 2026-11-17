<script setup>
import { computed } from 'vue';

const props = defineProps({
    bids: { type: Array, required: true },
});

// con esto se muestran las 10 pujas más altas (la lista ya viene ordenada de mayor a menor).
const latest = computed(() => props.bids.slice(0, 10));
</script>


<template>
    <div>
        <p v-if="latest.length === 0" class="text-body-secondary mb-0">
            Todavía no hay pujas en esta subasta.
        </p>

        <div v-for="bid in latest" :key="bid.id" class="tr-activity">
            <span class="flex-grow-1">
                {{ bid.user }}
                <span class="tr-meta text-body-secondary d-block">{{ bid.created_at_human }}</span>
            </span>
            <span class="tr-price">{{ bid.amount_label }}</span>
        </div>
    </div>
</template>