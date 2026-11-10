@extends('layouts.app')

@section('title', 'Compra, intercambio y subasta de hardware')

@section('content')
    @php
        $catalogUrl = Route::has('listings.index') ? route('listings.index') : '#publicaciones';
    @endphp

    <div class="tr-home-page">
        <section class="tr-home-hero">
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-7">
                        <h1 class="tr-home-hero__title">Donde el hardware encuentra <span>su próximo destino</span></h1>
                        <p class="tr-home-hero__text">Comprá, vendé e intercambiá componentes de PC en un espacio pensado para quienes disfrutan del hardware.</p>
                        <a href="{{ $catalogUrl }}" class="btn btn-outline-tr tr-home-hero__button">
                            Ver componentes <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <div class="col-lg-5 text-center">
                        <img src="{{ asset('img/logo-full-light.png') }}" alt="TRADERIG" class="tr-home-hero__logo">
                    </div>
                </div>
            </div>
        </section>

        <section class="container tr-home-section" id="categorias">
            <header class="tr-section">
                <h2 class="tr-section__title">¿Qué estás buscando?</h2>
            </header>

            <div class="tr-home-category-grid">
                @forelse ($categories as $category)
                    @php $tag = Route::has('listings.index') ? 'a' : 'div'; @endphp
                    <{{ $tag }} class="tr-home-category"
                        @if ($tag === 'a') href="{{ route('listings.index', ['category' => $category->slug]) }}" @endif>
                        <span class="tr-home-category__icon" aria-hidden="true">{{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}</span>
                        <span>{{ $category->name }}</span>
                    </{{ $tag }}>
                @empty
                    <p class="text-body-secondary mb-0">Todavía no hay categorías cargadas.</p>
                @endforelse
            </div>
        </section>

        <section class="tr-home-section tr-home-featured" id="publicaciones">
            <div class="container">
                <header class="tr-section">
                    <h2 class="tr-section__title">Publicaciones destacadas</h2>
                    @if (Route::has('listings.index'))
                        <a href="{{ route('listings.index') }}" class="tr-section__link">Ver todas <span aria-hidden="true">→</span></a>
                    @endif
                </header>

                <div class="row g-3">
                    @forelse ($featured as $listing)
                        <div class="col-sm-6 col-lg-3"><x-listing-card :listing="$listing" /></div>
                    @empty
                        <p class="text-body-secondary">Todavía no hay publicaciones. Cuando se carguen, las más nuevas aparecen acá.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="container tr-home-section" id="intercambios">
            <header class="tr-section">
                <h2 class="tr-section__title">Intercambios disponibles</h2>
            </header>

            <div class="row g-3">
                @forelse ($trades as $listing)
                    <div class="col-lg-6">
                        <a href="{{ route('listings.show', $listing) }}" class="tr-home-trade">
                            <div class="tr-home-trade__media {{ $listing->mainPhoto?->path ? '' : 'is-placeholder' }}">
                                @if ($listing->mainPhoto?->path)
                                    <img src="{{ asset('storage/'.$listing->mainPhoto->path) }}" alt="" loading="lazy">
                                @else
                                    <img src="{{ asset('img/logo-icon-light.png') }}" alt="" loading="lazy">
                                @endif
                            </div>
                            <div class="tr-home-trade__content">
                                <span class="tr-badge">Intercambio</span>
                                <h3>{{ $listing->title }}</h3>
                                <p>{{ $listing->condition_label }} · {{ $listing->category->name }}</p>
                                <span class="tr-home-trade__offer">Acepta propuestas</span>
                                @if ($listing->city)
                                    <span class="tr-home-trade__location">{{ $listing->city }}</span>
                                @endif
                                <span class="tr-home-trade__link">Consultar intercambio <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="text-body-secondary">Todavía no hay publicaciones disponibles para intercambiar.</p>
                @endforelse
            </div>
        </section>

        <section class="container tr-home-section tr-home-auctions" id="subastas">
            <header class="tr-section">
                <div>
                    <h2 class="tr-section__title">Subastas activas</h2>
                    <p class="tr-home-section__description">Seguí las ofertas y participá antes de que termine el tiempo.</p>
                </div>
                @if (Route::has('auctions.index'))
                    <a href="{{ route('auctions.index') }}" class="tr-section__link">Ver todas <span aria-hidden="true">→</span></a>
                @endif
            </header>

            <div class="row g-3">
                @forelse ($auctions as $listing)
                    <div class="col-sm-6 col-lg-4"><x-listing-card :listing="$listing" /></div>
                @empty
                    <p class="text-body-secondary">No hay subastas abiertas en este momento.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
