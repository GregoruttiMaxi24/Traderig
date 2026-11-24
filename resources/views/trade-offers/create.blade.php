@extends('layouts.app')

@section('title', 'Proponer intercambio')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <h1 class="h3 mb-1">Proponer intercambio</h1>
                <p class="tr-meta text-body-secondary mb-4">
                    Querés: {{ $listing->title }} · Publicado por {{ $listing->user->name }}
                </p>

                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('trade-offers.store', $listing) }}">
                            @csrf

             {{-- publicación que ofrece el intersado a cambio ( es opcional) --}}
                            <div class="mb-3">

                                <label for="offered_listing_id" class="form-label">
                                    Qué ofrecés a cambio
                                    <span class="text-body-secondary">(opcional)</span>

                                </label>

                                <select id="offered_listing_id" name="offered_listing_id"
                                        class="form-select

                                         @error ('offered_listing_id') is-invalid @enderror">

                                    <option value="">No ofrezco una publicación (solo dinero)</option>

                                    @foreach ($myListings as $myListing)
                                        <option value="{{ $myListing->id }}" @selected(old('offered_listing_id') == $myListing->id)>
                                            {{ $myListing->title }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('offered_listing_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($myListings->isEmpty())

                                    <div class="form-text">
                                        No tenés publicaciones de venta activas para ofrecer.
                                        <a href="{{ route('listings.create') }}">Publicar un componente</a>
                                    </div>

                                @endif
                            </div>

                            {{-- diferencia de dinero (opcional) --}}
                            <div class="row">
                                <div class="col-md-6">

                                   <div class="mb-3">
                                        <label for="cash_amount" class="form-label mt-4">
                                            Diferencia de dinero ($)
                                            <span class="text-body-secondary">(opcional)</span>
                                        </label>

                                        <input id="cash_amount" name="cash_amount" type="number"
                                            value="{{ old('cash_amount') }}"
                                            class="form-control 
                                            @error('cash_amount') is-invalid @enderror">
        

                                        @error('cash_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                         <span class="form-text">Dejalo vacío o en 0 si no hay diferencia.</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">

                                        <label for="cash_direction" class="form-label">
                                            Quién paga la diferencia
                                            <span class="text-body-secondary">(si hay dinero)</span>

                                        </label>

                                        <select id="cash_direction" name="cash_direction"

                                                class="form-select 
                                                @error('cash_direction') is-invalid @enderror">
                                            <option value="">Elegí una opción</option>
                                            <option value="offerer_pays" @selected(old('cash_direction') === 'offerer_pays')>Pago yo</option>
                                            <option value="owner_pays" @selected(old('cash_direction') === 'owner_pays')>Paga el dueño</option>

                                        </select>

                                        @error('cash_direction')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>
                                </div>
                            </div>

                            {{-- mensaje para el dueño --}}
                            <div class="mb-3">
                                <label for="message" class="form-label">
                                    Mensaje
                                    <span class="text-body-secondary">(opcional)</span>
                                </label>

                                <textarea id="message" name="message" rows="4"
                                          class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>

                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Enviar propuesta</button>
                            <a href="{{ route('listings.show', $listing) }}" class="btn btn-outline-tr">Cancelar</a>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection