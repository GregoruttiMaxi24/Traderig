@extends('layouts.app')

@section('title', $listing->title)

@section('content')
    @php
        $auction = $listing->auction;
        $photo = $galleryPhotos->first();
    @endphp

    <div class="container py-4 py-lg-5 tr-listing-detail">
        <nav aria-label="Navegación de la publicación" class="tr-listing-detail__breadcrumb">
            <a href="{{ route('home') }}">Inicio</a>
            <span aria-hidden="true">/</span>
            <span>{{ $listing->category->name }}</span>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $listing->title }}</span>
        </nav>

        <header class="tr-listing-detail__header">
            <div class="tr-listing-detail__badges">
                <span class="tr-badge tr-listing-detail__badge--type">{{ $listing->type_label }}</span>
                <span class="tr-badge tr-listing-detail__badge--condition">{{ $listing->condition_label }}</span>
                @if ($listing->accepts_trade)
                    <span class="tr-badge tr-listing-detail__badge--trade">Acepta intercambio</span>
                @endif
                @if ($listing->status !== 'active')
                    <span class="tr-badge tr-listing-detail__badge--status">
                        {{ \App\Models\Listing::STATUSES[$listing->status] ?? $listing->status }}
                    </span>
                @endif
            </div>
            <h1 class="tr-listing-detail__title">{{ $listing->title }}</h1>
            <p class="tr-listing-detail__byline">
                Publicado {{ $listing->created_at->format('d/m/Y') }}
                @if ($listing->city)
                    <span aria-hidden="true">·</span>
                    {{ $listing->city }}
                @endif
                <span aria-hidden="true">·</span>
                <a href="{{ route('users.show', $listing->user) }}">{{ $listing->user->name }}</a>
            </p>
        </header>

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <section class="tr-gallery" aria-label="Fotos de {{ $listing->title }}" data-listing-gallery>
                    @if ($photo)
                        <div class="tr-gallery__main">
                            @foreach ($galleryPhotos as $galleryPhoto)
                                <img
                                    src="{{ asset('storage/'.$galleryPhoto->path) }}"
                                    alt="{{ $listing->title }}. Foto {{ $loop->iteration }}"
                                    class="tr-gallery__image @if ($loop->first) is-active @endif"
                                    data-gallery-image
                                    @if (! $loop->first) hidden @endif
                                >
                            @endforeach
                            <button type="button" class="tr-gallery__expand" data-gallery-open aria-label="Ampliar foto">
                                Ampliar
                            </button>
                            @if ($galleryPhotos->count() > 1)
                                <button
                                    type="button"
                                    class="tr-gallery__step tr-gallery__step--previous"
                                    data-gallery-previous
                                    aria-label="Foto anterior"
                                >
                                    <span aria-hidden="true">‹</span>
                                </button>
                                <button
                                    type="button"
                                    class="tr-gallery__step tr-gallery__step--next"
                                    data-gallery-next
                                    aria-label="Foto siguiente"
                                >
                                    <span aria-hidden="true">›</span>
                                </button>
                                <span class="tr-gallery__counter" data-gallery-counter aria-live="polite">
                                    1 / {{ $galleryPhotos->count() }}
                                </span>
                            @endif
                        </div>
                        @if ($galleryPhotos->count() > 1)
                            <div class="tr-gallery__thumbs" role="group" aria-label="Elegir foto">
                                @foreach ($galleryPhotos as $galleryPhoto)
                                    <button
                                        type="button"
                                        class="tr-gallery__thumb @if ($loop->first) is-active @endif"
                                        data-gallery-thumb
                                        data-photo-index="{{ $loop->index }}"
                                        aria-label="Ver foto {{ $loop->iteration }}"
                                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    >
                                        <img src="{{ asset('storage/'.$galleryPhoto->path) }}" alt="" loading="lazy">
                                        <span>{{ $loop->first ? 'Foto principal' : 'Foto '.$loop->iteration }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <dialog class="tr-gallery__lightbox" data-gallery-dialog aria-label="Foto ampliada">
                            <div class="tr-gallery__lightbox-toolbar">
                                @if ($galleryPhotos->count() > 1)
                                    <button type="button" data-gallery-previous aria-label="Foto anterior">Anterior</button>
                                @else
                                    <span></span>
                                @endif
                                <button type="button" data-gallery-close>Cerrar</button>
                                @if ($galleryPhotos->count() > 1)
                                    <button type="button" data-gallery-next aria-label="Foto siguiente">Siguiente</button>
                                @endif
                            </div>
                            <img
                                src="{{ asset('storage/'.$photo->path) }}"
                                alt="{{ $listing->title }}. Foto 1"
                                data-gallery-dialog-image
                            >
                        </dialog>
                    @else
                        <div class="tr-gallery__main">
                            <div class="tr-ph"><img src="{{ asset('img/logo-icon-light.png') }}" alt=""></div>
                        </div>
                        <p class="tr-gallery__empty">El vendedor todavía no agregó fotos.</p>
                    @endif
                </section>

                <section class="card tr-listing-detail__section tr-listing-detail__section--description">
                    <div class="card-body">
                        <h2 class="tr-listing-detail__section-title">Descripción del producto</h2>
                        <p class="tr-listing-detail__description">{{ $listing->description }}</p>
                        @if ($listing->seller_notes)
                            <div class="tr-listing-detail__seller-notes">
                                <h3>Observaciones del vendedor</h3>
                                <p>{{ $listing->seller_notes }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="card tr-listing-detail__section tr-listing-detail__section--specifications">
                    <div class="card-body">
                        <h2 class="tr-listing-detail__section-title">Especificaciones</h2>
                        <dl class="tr-listing-detail__specs">
                            @if ($listing->brand)
                                <div class="tr-listing-detail__spec-row">
                                    <dt>Marca</dt>
                                    <dd>{{ $listing->brand }}</dd>
                                </div>
                            @endif

                            @forelse ($listing->specifications ?? [] as $specification)
                                <div class="tr-listing-detail__spec-row">
                                    <dt>{{ $specification['name'] }}</dt>
                                    <dd>{{ $specification['value'] }}</dd>
                                </div>
                            @empty
                                <div class="tr-listing-detail__spec-row">
                                    <dt>Especificaciones</dt>
                                    <dd>El vendedor no agregó características técnicas.</dd>
                                </div>
                            @endforelse

                            <div class="tr-listing-detail__spec-row">
                                <dt>Categoría</dt>
                                <dd>{{ $listing->category->name }}</dd>
                            </div>

                            <div class="tr-listing-detail__spec-row">
                                <dt>Estado</dt>
                                <dd>{{ $listing->condition_label }}</dd>
                            </div>

                            <div class="tr-listing-detail__spec-row">
                                <dt>Tipo de publicación</dt>
                                <dd>{{ $listing->type_label }}</dd>
                            </div>

                            @if ($listing->city)
                                <div class="tr-listing-detail__spec-row">
                                    <dt>Ubicación</dt>
                                    <dd>{{ $listing->city }}</dd>
                                </div>
                            @endif

                            <div class="tr-listing-detail__spec-row">
                                <dt>Intercambio</dt>
                                <dd>{{ $listing->accepts_trade ? 'Acepta propuestas' : 'No acepta' }}</dd>
                            </div>

                            @if ($auction)
                                <div class="tr-listing-detail__spec-row">
                                    <dt>Finaliza</dt>
                                    <dd>{{ $auction->ends_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </section>
            </div>

            <aside class="col-lg-5">
                @if ($auction)
                    <div
                        id="auction-panel"
                        data-state-url="{{ route('auctions.state', $auction) }}"
                        data-bid-url="{{ route('auctions.bids.store', $auction) }}"
                        data-login-url="{{ route('login') }}"
                        data-seller-id="{{ $listing->user_id }}"
                        data-starting-price-label="{{ \App\Support\Money::format($auction->starting_price) }}"
                        data-min-increment-label="{{ \App\Support\Money::format($auction->min_increment) }}"
                        data-bid-count="{{ $auction->bids_count }}"
                        data-initial-is-open="{{ $auction->isOpen() ? 'true' : 'false' }}"
                    ></div>
                @else
                    <section class="card tr-listing-detail__summary tr-listing-detail__summary--sale">
                        <div class="card-body">
                            <span class="tr-label">Precio de venta</span>
                            <p class="tr-price tr-listing-detail__price">{{ \App\Support\Money::format($listing->price) }}</p>
                        </div>
                    </section>
                @endif

                @if ($listing->accepts_trade)
                    <section class="card tr-listing-detail__trade">
                        <div class="card-body">
                            @if (Route::has('trade-offers.create'))
                            <a href="{{ route('trade-offers.create', $listing) }}" class="btn btn-primary w-100">
                                Proponer intercambio
                            </a>
                            @else
                                <button type="button" class="btn btn-outline-tr w-100" disabled>
                                    Proponer intercambio
                                </button>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="card tr-listing-detail__seller">
                    <div class="card-body">
                        <h2 class="tr-listing-detail__section-title">Información del vendedor</h2>
                        <div class="tr-listing-detail__seller-profile">
                            <x-avatar :user="$listing->user" size="lg" />
                            <div>
                                <a class="tr-listing-detail__seller-name" href="{{ route('users.show', $listing->user) }}">
                                    {{ $listing->user->name }}
                                </a>
                                @if ($listing->city)
                                    <p class="tr-listing-detail__seller-location">{{ $listing->city }}</p>
                                @endif
                                <p>
                                    {{ $sellerListingsCount }}
                                    {{ $sellerListingsCount === 1 ? 'publicación activa' : 'publicaciones activas' }}
                                </p>
                            </div>
                        </div>
                        <div class="tr-listing-detail__seller-actions">
                            <a href="{{ route('users.show', $listing->user) }}" class="btn btn-outline-tr">Ver perfil</a>
                            <button type="button" class="btn btn-outline-tr" data-copy-link>Copiar enlace</button>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/app.js')
@endpush
