@extends('layouts.app')

@section('title', 'Compra, intercambio y subasta de hardware')

@section('content')
    @php
        $catalogUrl = Route::has('listings.index') ? route('listings.index') : '#categorias';
    @endphp

    <section class="tr-hero">
        <div class="container position-relative">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7 order-2 order-lg-1">
                    <h1 class="display-5">Tu próxima evolución empieza acá</h1>
                    <p class="lead mt-3">Comprá, intercambiá o subastá componentes de PC usados, con una comunidad que sabe de hardware.</p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ $catalogUrl }}" class="btn btn-accent btn-lg">Ver componentes</a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Crear cuenta</a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 text-center">
                    <img src="{{ asset('img/logo-full-light.png') }}" alt="TRADERIG" class="tr-hero-logo">
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
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

    <section class="container pb-4" id="categorias">
        <h2 class="h3 mb-3">Categorías</h2>

        <div class="row g-3">
            @forelse ($categories as $category)
                <div class="col-6 col-md-4 col-lg-3">
                    @if (Route::has('listings.index'))
                        <a class="tr-tile" href="{{ route('listings.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                    @else
                        <span class="tr-tile">{{ $category->name }}</span>
                    @endif
                </div>
            @empty
                <p class="text-body-secondary">Todavía no hay categorías cargadas.</p>
            @endforelse
        </div>
    </section>
@endsection
