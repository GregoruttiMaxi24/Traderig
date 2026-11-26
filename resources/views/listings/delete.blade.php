@extends('layouts.app')

@section('title', 'Eliminar publicación')

@section('content')
    <div class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <a href="{{ route('listings.mine') }}" class="tr-listing-form__back">← Volver a mis publicaciones</a>

                <header class="tr-listing-form__header">

                    <h1>¿Deseás eliminar esta publicación?</h1>

                    <p>Revisá lo que se va a eliminar antes de confirmar.</p>
                </header>

                <article class="card mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-center">

                            @if ($listing->mainPhoto !== null)
                                <div class="col-md-4">
                                   <img src="{{ asset('storage/'.$listing->mainPhoto->path) }}" alt="{{ $listing->title }}" class="tr-delete-photo rounded-2 shadow-sm">
                                </div>
                            @endif

                            <div class="col-md">
                                <h2 class="h4">{{ $listing->title }}</h2>
                                <p class="tr-meta text-body-secondary">
                                    {{ $listing->type_label }} · {{ \App\Support\Money::format($listing->price) }} · Publicada el {{ $listing->created_at->format('d/m/Y') }}
                                </p>

                                <p class="mb-0">{{ $listing->description }}</p>
                            </div>

                        </div>
                    </div>
                </article>

                <div class="tr-delete-warning mb-4">
                    <p class="mb-0">Esta acción no se puede deshacer. Se van a eliminar también:</p>
                    <ul>
                        <li>Las fotos de la publicación.</li>
                        <li>{{ $bidsCount }} {{ $bidsCount === 1 ? 'puja' : 'pujas' }}.</li>
                        <li>{{ $offersCount }} {{ $offersCount === 1 ? 'propuesta de intercambio' : 'propuestas de intercambio' }}.</li>
                        <li>{{ $chatsCount }} {{ $chatsCount === 1 ? 'chat' : 'chats' }} con sus mensajes.</li>
                    </ul>
                </div>

                <form action="{{ route('listings.destroy', $listing) }}" method="POST" class="d-flex justify-content-center gap-2">

                    @csrf

                    <a href="{{ route('listings.mine') }}" class="btn btn-primary">Cancelar</a>
                    <button type="submit" class="btn btn-danger-tr">Eliminar publicación</button>
                </form>

            </div>
        </div>
    </div>


@endsection