@extends('layouts.app')

@section('title', $listing->title)

@section('content')
    @php
        $auction = $listing->auction;
        $photo = $listing->photos->first();
    @endphp

    <div class="container py-4">
        <div class="row g-4 align-items-start">
            {{-- Foto --}}
            <div class="col-lg-7">
                <div class="tr-detail-media">
                    @if ($photo)
                        <img src="{{ asset('storage/'.$photo->path) }}" alt="{{ $listing->title }}">
                    @else
                        <div class="tr-ph"><img src="{{ asset('img/logo-icon-light.png') }}" alt=""></div>
                    @endif
                </div>
            </div>

            {{-- Panel de información --}}
            <div class="col-lg-5">
                <div class="card px-3 py-4">
                    <div class="card-body p-4">
                        <h1 class="tr-title h2 mb-1">{{ $listing->title }}</h1>
                        <p class="mb-3">
                            <a href="{{ route('users.show', $listing->user) }}">{{ $listing->user->name }}</a>
                            <span class="tr-meta text-body-secondary"> · {{ $listing->category->name }}</span>
                        </p>

                        <div class="tr-badges tr-badges--inline mb-3">
                            @if ($auction)<span class="tr-badge mb-3">Subasta</span>@endif
                            @if ($listing->accepts_trade)<span class="tr-badge">Acepta intercambio</span>@endif
                            @if ($listing->status !== 'active')<span class="tr-badge">{{ \App\Models\Listing::STATUSES[$listing->status] ?? $listing->status }}</span>@endif
                        </div>

                        @if ($auction)
            {{-- este es el panel de Vue: precio actual, cuenta regresiva y últimas pujas --}}
                    <div id="auction-panel" data-state-url="{{ route('auctions.state', $auction) }}"></div>
                      @else
                            <span class="tr-label">Precio</span>
                            <p class="tr-price display-6 mb-0">{{ \App\Support\Money::format($listing->price) }}</p>
                     @endif

                 {{-- Acciones: puja (si la subasta está abierta) e intercambio --}}
                        <div class="d-grid gap-2 my-4">
                            @if ($auction && $auction->isOpen())
                                @php $minBid = $auction->minimumNextBid(); @endphp
                                @auth
                                    @if (auth()->id() === $listing->user_id)
                                        <p class="tr-meta text-body-secondary mb-0">Esta es tu subasta: no podés pujar en ella.</p>
                                    @else

                                        <form method="POST" action="{{ route('auctions.bids.store', $auction) }}" class="tr-bid-box" novalidate>
                                            @csrf
                                            <label for="amount" class="tr-bid-title">Hacé tu puja</label>
                                            <div class="input-group input-group-lg has-validation">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="amount" id="amount"
                                                    class="form-control @error('amount') is-invalid @enderror"
                                                    min="{{ $minBid }}" max="6000000" step="1" value="{{ old('amount', $minBid) }}" required>
                                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <p class="tr-bid-hint">Puja mínima: <span class="tr-price">{{ \App\Support\Money::format($minBid) }}</span></p>
                                            <button type="submit" class="btn btn-primary btn-lg w-100">Pujar</button>
                                        </form>

                                    @endif
                                    
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Ingresá para pujar</a>
                                @endauth
                            @endif

                            @if ($listing->accepts_trade)
                                @if (Route::has('trade-offers.create'))
                                    <a href="{{ route('trade-offers.create', $listing) }}" class="btn btn-outline-tr btn-lg">Proponer intercambio</a>
                                @else
                                    <button type="button" class="btn btn-outline-tr btn-lg" disabled>Proponer intercambio</button>
                                @endif
                            @endif
                        </div>

                        <h2 class="tr-subtitle py-3">Descripción</h2>
                        <p class="mb-4">{{ $listing->description }}</p>

                        <div class="accordion" id="listing-details">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#details-body" aria-expanded="true" aria-controls="details-body">Detalles</button>
                                </h2>
                                <div id="details-body" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <dl class="tr-specs">
                                            <dt>Categoría</dt><dd>{{ $listing->category->name }}</dd>
                                            <dt>Estado</dt><dd>{{ $listing->condition_label }}</dd>
                                            @if ($listing->brand)<dt>Marca</dt><dd>{{ $listing->brand }}</dd>@endif
                                            @if ($listing->city)<dt>Ubicación</dt><dd>{{ $listing->city }}</dd>@endif
                                            <dt>Tipo</dt><dd>{{ $listing->type_label }}</dd>
                                            <dt>Intercambio</dt><dd>{{ $listing->accepts_trade ? 'Acepta ofertas' : 'No acepta' }}</dd>
                                            @if ($auction)<dt>Cierra</dt><dd class="tr-meta">{{ $auction->ends_at->format('d/m/Y H:i') }}</dd>@endif
                                            <dt>Publicada</dt><dd class="tr-meta">{{ $listing->created_at->format('d/m/Y') }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="tr-divider">

                        <h2 class="tr-subtitle my-3">Vendedor</h2>
                        <div class="d-flex align-items-center gap-3">
                            <x-avatar :user="$listing->user" size="lg" />
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $listing->user->name }}</div>
                                <div class="tr-meta text-body-secondary">{{ $sellerListingsCount }} {{ $sellerListingsCount === 1 ? 'publicación activa' : 'publicaciones activas' }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mt-5">
                            <button type="button" class="btn btn-outline-tr" data-copy-link>Copiar enlace</button>
                            <a href="{{ route('users.show', $listing->user) }}" class="btn btn-outline-tr">Ver perfil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/app.js')
@endpush