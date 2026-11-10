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
        startingPriceLabel: auctionPanel.dataset.startingPriceLabel,
        minIncrementLabel: auctionPanel.dataset.minIncrementLabel,
        initialBidCount: Number(auctionPanel.dataset.bidCount),
        initialIsOpen: auctionPanel.dataset.initialIsOpen === 'true',
    }).mount(auctionPanel);
}

const photoInput = document.querySelector('[data-photo-input]');
const photoPreview = document.querySelector('[data-photo-preview]');

if (photoInput && photoPreview) {
    let previewUrls = [];

    photoInput.addEventListener('change', () => {
        previewUrls.forEach((url) => URL.revokeObjectURL(url));
        previewUrls = [];
        photoPreview.replaceChildren();

        Array.from(photoInput.files ?? []).forEach((file, index) => {
            if (!file.type.startsWith('image/')) {
                return;
            }

            const url = URL.createObjectURL(file);
            previewUrls.push(url);

            const preview = document.createElement('figure');
            preview.className = 'tr-upload__preview';

            const image = document.createElement('img');
            image.src = url;
            image.alt = `Vista previa: ${file.name}`;

            const caption = document.createElement('figcaption');
            caption.textContent = index === 0 ? 'Portada' : `Foto ${index + 1}`;

            preview.append(image, caption);
            photoPreview.append(preview);
        });
    });
}

const specificationEditor = document.querySelector('[data-specifications]');

if (specificationEditor) {
    const specificationList = specificationEditor.querySelector('[data-specifications-list]');
    const specificationTemplate = specificationEditor.querySelector('[data-specification-template]');
    const addSpecificationButton = specificationEditor.querySelector('[data-specification-add]');
    const maxSpecifications = Number(specificationList.dataset.max);
    const initialRows = Array.from(specificationList.querySelectorAll('[data-specification-row]'));
    let nextSpecificationIndex = initialRows.reduce(
        (nextIndex, row) => Math.max(nextIndex, Number(row.dataset.index) + 1),
        0,
    );

    const updateSpecificationControls = () => {
        const rows = specificationList.querySelectorAll('[data-specification-row]');
        addSpecificationButton.disabled = rows.length >= maxSpecifications;

        rows.forEach((row) => {
            row.querySelector('[data-specification-remove]').disabled = rows.length <= 1;
        });
    };

    addSpecificationButton.addEventListener('click', () => {
        const rowMarkup = specificationTemplate.innerHTML.replaceAll(
            '__INDEX__',
            String(nextSpecificationIndex),
        );
        nextSpecificationIndex += 1;
        specificationList.insertAdjacentHTML('beforeend', rowMarkup);
        updateSpecificationControls();
        specificationList.querySelector('[data-specification-row]:last-child input')?.focus();
    });

    specificationList.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-specification-remove]');

        if (!removeButton) {
            return;
        }

        removeButton.closest('[data-specification-row]').remove();
        updateSpecificationControls();
    });

    updateSpecificationControls();
}

document.querySelectorAll('[data-listing-gallery]').forEach((gallery) => {
    const images = gallery.querySelectorAll('[data-gallery-image]');
    const counter = gallery.querySelector('[data-gallery-counter]');
    const thumbnails = gallery.querySelectorAll('[data-gallery-thumb]');
    const dialog = gallery.querySelector('[data-gallery-dialog]');
    const dialogImage = gallery.querySelector('[data-gallery-dialog-image]');

    const showPhoto = (index) => {
        const activeIndex = (index + images.length) % images.length;

        images.forEach((image, imageIndex) => {
            image.hidden = imageIndex !== activeIndex;
        });

        thumbnails.forEach((thumbnail, thumbnailIndex) => {
            const isActive = thumbnailIndex === activeIndex;

            thumbnail.classList.toggle('is-active', isActive);
            thumbnail.setAttribute('aria-pressed', String(isActive));
        });

        if (counter) {
            counter.textContent = `${activeIndex + 1} / ${images.length}`;
        }

        if (dialogImage) {
            dialogImage.src = images[activeIndex].src;
            dialogImage.alt = images[activeIndex].alt;
        }

        return activeIndex;
    };

    gallery.addEventListener('click', (event) => {
        const control = event.target.closest(
            '[data-gallery-thumb], [data-gallery-previous], [data-gallery-next], [data-gallery-open], [data-gallery-close]',
        );

        if (!control) {
            return;
        }

        if (control.hasAttribute('data-gallery-thumb')) {
            showPhoto(Number(control.dataset.photoIndex));
        } else if (control.hasAttribute('data-gallery-previous')) {
            showPhoto(Array.from(images).findIndex((image) => !image.hidden) - 1);
        } else if (control.hasAttribute('data-gallery-next')) {
            showPhoto(Array.from(images).findIndex((image) => !image.hidden) + 1);
        } else if (control.hasAttribute('data-gallery-open') && dialog?.showModal) {
            showPhoto(Array.from(images).findIndex((image) => !image.hidden));
            dialog.showModal();
        } else if (control.hasAttribute('data-gallery-close')) {
            dialog?.close();
        }
    });

    dialog?.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
});
