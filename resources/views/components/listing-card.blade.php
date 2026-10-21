@props(['listing'])
@php
    $photo = $listing->mainPhoto?->path;
    $auction = $listing->isAuction() ? $listing->auction : null;
    $hasBids = $auction && $auction->current_price > $auction->starting_price;
@endphp
<a href="{{ route('listings.show', $listing) }}" class="tr-card tr-listing">
    <div class="tr-listing__media">
        @if ($photo)
            <img src="{{ asset('storage/'.$photo) }}" alt="{{ $listing->title }}" loading="lazy">
        @else
            <div class="tr-ph"><img src="{{ asset('img/logo-icon-light.png') }}" alt=""></div>
        @endif
        <div class="tr-badges">
            @if ($auction)<span class="tr-badge">Subasta</span>@endif
            @if ($listing->accepts_trade)<span class="tr-badge">Acepta intercambio</span>@endif
        </div>
    </div>
    <div class="tr-listing__body">
        <h3 class="tr-listing__title">{{ $listing->title }}</h3>
        <p class="tr-meta text-body-secondary mb-0">{{ $listing->user->name }} · {{ $listing->category->name }}</p>
        <div class="tr-listing__foot">
            @if ($auction)
                <div>
                    <span class="tr-label">{{ $hasBids ? 'Puja actual' : 'Puja inicial' }}</span>
                    <span class="tr-price">{{ \App\Support\Money::format($auction->current_price) }}</span>
                </div>
                <div class="text-end">
                    <span class="tr-label">Cierra en</span>
                    <span class="tr-meta" data-countdown="{{ $auction->ends_at->toIso8601String() }}">{{ $auction->ends_at->format('d/m H:i') }}</span>
                </div>
            @else
                <div>
                    <span class="tr-label">Precio</span>
                    <span class="tr-price">{{ \App\Support\Money::format($listing->price) }}</span>
                </div>
                <span class="tr-meta text-body-secondary">{{ $listing->condition_label }}</span>
            @endif
        </div>
    </div>
</a>
