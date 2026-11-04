import { createApp } from 'vue';
import AuctionPanel from './components/auction/AuctionPanel.vue';

// el panel de de la subasta que solo se monta en la página que tiene el contenedor
const auctionPanel = document.getElementById('auction-panel');

if (auctionPanel) {
    createApp(AuctionPanel, {
        stateUrl: auctionPanel.dataset.stateUrl,
        bidUrl: auctionPanel.dataset.bidUrl,
        loginUrl: auctionPanel.dataset.loginUrl,
        sellerId: Number(auctionPanel.dataset.sellerId),
    }).mount(auctionPanel);
}
