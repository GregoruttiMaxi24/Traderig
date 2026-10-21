@extends('layouts.app')

@section('title', 'Compra, intercambio y subasta de hardware')

@section('content')
    @php
        $catalogUrl = Route::has('listings.index') ? route('listings.index') : '#categorias';
    @endphp

    {{-- Portada --}}
    <section class="tr-hero">
        <div class="container position-relative">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7 order-2 order-lg-1">
                    <h1 class="display-4 tr-title">Tu próxima evolución empieza acá</h1>
                    <p class="lead mt-3">Comprá, intercambiá o subastá componentes de PC usados, con una comunidad que sabe de hardware.</p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ $catalogUrl }}" class="btn btn-accent btn-lg">Ver componentes</a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-tr btn-lg">Crear cuenta</a>
                        @endguest
                    </div>
                    <dl class="tr-hero__stats">
                        <div><dt>Publicaciones</dt><dd>{{ $stats['listings'] }}</dd></div>
                        <div><dt>Subastas en vivo</dt><dd>{{ $stats['auctions'] }}</dd></div>
                        <div><dt>Miembros</dt><dd>{{ $stats['members'] }}</dd></div>
                    </dl>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 text-center">
                    <img src="{{ asset('img/logo-full-light.png') }}" alt="TRADERIG" class="tr-hero-logo">
                </div>
            </div>
        </div>
    </section>

    {{-- Publicaciones destacadas --}}
    <section class="container py-5">
        <header class="tr-section">
            <h2 class="tr-section__title">Publicaciones destacadas</h2>
            @if (Route::has('listings.index'))
                <a href="{{ route('listings.index') }}" class="tr-section__link">Ver todas ›</a>
            @endif
        </header>

        <div class="row g-4">
            @forelse ($featured as $listing)
                <div class="col-md-6 col-lg-4"><x-listing-card :listing="$listing" /></div>
            @empty
                <p class="text-body-secondary">Todavía no hay publicaciones. Cuando se carguen, las más nuevas aparecen acá.</p>
            @endforelse
        </div>
    </section>

    {{-- Categorías --}}
    <section class="container py-4" id="categorias">
        <header class="tr-section">
            <h2 class="tr-section__title">Categorías</h2>
        </header>

        <div class="row g-3">
            @forelse ($categories as $category)
                <div class="col-md-6 col-lg-4">
                    @php $tag = Route::has('listings.index') ? 'a' : 'div'; @endphp
                    <{{ $tag }} class="tr-rank" @if ($tag === 'a') href="{{ route('listings.index', ['category' => $category->slug]) }}" @endif>
                        <span class="tr-rank__n">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="tr-rank__name">{{ $category->name }}</span>
                        <span class="tr-meta text-body-secondary">{{ $category->active_count }} {{ $category->active_count === 1 ? 'publicación' : 'publicaciones' }}</span>
                    </{{ $tag }}>
                </div>
            @empty
                <p class="text-body-secondary">Todavía no hay categorías cargadas.</p>
            @endforelse
        </div>
    </section>

    {{-- Subastas en vivo --}}
    <section class="container py-5">
        <header class="tr-section">
            <h2 class="tr-section__title">Subastas en vivo</h2>
            @if (Route::has('auctions.index'))
                <a href="{{ route('auctions.index') }}" class="tr-section__link">Ver todas ›</a>
            @endif
        </header>

        <div class="row g-4">
            @forelse ($auctions as $listing)
                <div class="col-sm-6 col-lg-3"><x-listing-card :listing="$listing" /></div>
            @empty
                <p class="text-body-secondary">No hay subastas abiertas en este momento.</p>
            @endforelse
        </div>
    </section>

    {{-- Cómo funciona --}}
    <section class="container py-4">
        <div class="row g-4">
            <div class="col-md-4 tr-mode">
                <div class="tr-node" aria-hidden="true"><i></i><b></b></div>
                <h2 class="h4">Comprá</h2>
                <p>Mirá precio, estado y fotos reales, y escribile directo a quien vende.</p>
            </div>
            <div class="col-md-4 tr-mode">
                <div class="tr-node" aria-hidden="true"><i></i><b></b></div>
                <h2 class="h4">Intercambiá</h2>
                <p>Ofrecé una de tus piezas a cambio de otra, con diferencia de dinero si hace falta. El dueño acepta o rechaza.</p>
            </div>
            <div class="col-md-4 tr-mode">
                <div class="tr-node" aria-hidden="true"><i></i><b></b></div>
                <h2 class="h4">Subastá</h2>
                <p>Poné tu pieza con precio base y tiempo límite. Gana la puja más alta cuando termina la cuenta regresiva.</p>
            </div>
        </div>
    </section>

    {{-- Vendedores destacados --}}
    <section class="container py-5">
        <header class="tr-section">
            <h2 class="tr-section__title">Vendedores destacados</h2>
        </header>

        <div class="tr-panel">
            <div class="tr-panel__head"><span>Vendedor</span><span>Publicaciones</span></div>
            @forelse ($sellers as $seller)
                <a href="{{ route('users.show', $seller) }}" class="tr-panel__row">
                    <x-avatar :user="$seller" size="md" />
                    <span class="flex-grow-1">
                        {{ $seller->name }}
                        @if ($seller->city)<span class="tr-meta text-body-secondary d-block">{{ $seller->city }}</span>@endif
                    </span>
                    <span class="tr-price">{{ $seller->active_count }}</span>
                </a>
            @empty
                <p class="text-body-secondary mb-0 p-3">Todavía no hay vendedores con publicaciones activas.</p>
            @endforelse
        </div>
    </section>
@endsection
