@extends('layouts.app')

@section('title', $listing->title)

@section('content')
    @php
        $auction = $listing->auction;
        $photo = $listing->photos->first();
        $hasBids = $bids->isNotEmpty();
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
                <div class="card">
                    <div class="card-body p-4 p-xl-5">
                        <h1 class="tr-title h2 mb-1">{{ $listing->title }}</h1>
                        <p class="mb-3">
                            <a href="{{ route('users.show', $listing->user) }}">{{ $listing->user->name }}</a>
                            <span class="tr-meta text-body-secondary"> · {{ $listing->category->name }}</span>
                        </p>

                        <div class="tr-badges tr-badges--inline mb-3">
                            @if ($auction)<span class="tr-badge">Subasta</span>@endif
                            @if ($listing->accepts_trade)<span class="tr-badge">Acepta intercambio</span>@endif
                            @if ($listing->status !== 'active')<span class="tr-badge">{{ \App\Models\Listing::STATUSES[$listing->status] ?? $listing->status }}</span>@endif
                        </div>

                        @if ($auction)
                            <span class="tr-label">{{ $hasBids ? 'Puja actual' : 'Puja inicial' }}</span>
                            <p class="tr-price display-6 mb-1">{{ \App\Support\Money::format($auction->current_price) }}</p>
                            <p class="tr-meta mb-0">
                                @if ($auction->isOpen())
                                    Cierra en <span data-countdown="{{ $auction->ends_at->toIso8601String() }}">{{ $auction->ends_at->format('d/m H:i') }}</span>
                                @else
                                    Subasta cerrada
                                @endif
                            </p>
                        @else
                            <span class="tr-label">Precio</span>
                            <p class="tr-price display-6 mb-0">{{ \App\Support\Money::format($listing->price) }}</p>
                        @endif

                        {{-- Acciones: se activan cuando existan las rutas de las Fases 5 y 6 --}}
                        <div class="d-grid gap-2 my-4">
                            @if ($auction && $auction->isOpen())
                                @if (Route::has('auctions.bids.store'))
                                    <form method="POST" action="{{ route('auctions.bids.store', $auction) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg w-100">Pujar</button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-primary btn-lg" disabled>Pujar</button>
                                @endif
                            @endif

                            @if ($listing->accepts_trade)
                                @if (Route::has('trade-offers.create'))
                                    <a href="{{ route('trade-offers.create', $listing) }}" class="btn btn-outline-tr btn-lg">Proponer intercambio</a>
                                @else
                                    <button type="button" class="btn btn-outline-tr btn-lg" disabled>Proponer intercambio</button>
                                @endif
                            @endif
                        </div>

                        <h2 class="tr-subtitle">Descripción</h2>
                        <p class="mb-4">{{ $listing->description }}</p>

                        <ul class="nav tr-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-details" data-bs-toggle="tab" data-bs-target="#pane-details" type="button" role="tab" aria-controls="pane-details" aria-selected="true">Detalles</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-activity" data-bs-toggle="tab" data-bs-target="#pane-activity" type="button" role="tab" aria-controls="pane-activity" aria-selected="false">Actividad</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pane-details" role="tabpanel" aria-labelledby="tab-details" tabindex="0">
                                <dl class="tr-specs">
                                    <dt>Categoría</dt><dd>{{ $listing->category->name }}</dd>
                                    <dt>Estado</dt><dd>{{ $listing->condition_label }}</dd>
                                    @if ($listing->brand)<dt>Marca</dt><dd>{{ $listing->brand }}</dd>@endif
                                    @if ($listing->city)<dt>Ubicación</dt><dd>{{ $listing->city }}</dd>@endif
                                    <dt>Tipo</dt><dd>{{ $listing->type_label }}</dd>
                                    <dt>Intercambio</dt><dd>{{ $listing->accepts_trade ? 'Acepta ofertas' : 'No acepta' }}</dd>
                                    @if ($auction)<dt>Cierre</dt><dd class="tr-meta">{{ $auction->ends_at->format('d/m/Y H:i') }}</dd>@endif
                                    <dt>Publicada</dt><dd class="tr-meta">{{ $listing->created_at->format('d/m/Y') }}</dd>
                                </dl>
                            </div>
                            <div class="tab-pane fade" id="pane-activity" role="tabpanel" aria-labelledby="tab-activity" tabindex="0">
                                @forelse ($bids as $bid)
                                    <div class="tr-activity">
                                        <x-avatar :user="$bid->user" size="sm" />
                                        <span class="flex-grow-1">{{ $bid->user->name }}
                                            <span class="tr-meta text-body-secondary d-block">{{ $bid->created_at->diffForHumans() }}</span>
                                        </span>
                                        <span class="tr-price">{{ \App\Support\Money::format($bid->amount) }}</span>
                                    </div>
                                @empty
                                    <p class="text-body-secondary mb-0">Todavía no hay actividad en esta publicación.</p>
                                @endforelse
                            </div>
                        </div>

                        <hr class="tr-divider">

                        <h2 class="tr-subtitle">Vendedor</h2>
                        <div class="d-flex align-items-center gap-3">
                            <x-avatar :user="$listing->user" size="lg" />
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $listing->user->name }}</div>
                                <div class="tr-meta text-body-secondary">{{ $sellerListingsCount }} {{ $sellerListingsCount === 1 ? 'publicación activa' : 'publicaciones activas' }}</div>
                            </div>
                            <a href="{{ route('users.show', $listing->user) }}" class="btn btn-outline-tr">Ver perfil</a>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-outline-tr btn-sm" data-copy-link>Copiar enlace</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
