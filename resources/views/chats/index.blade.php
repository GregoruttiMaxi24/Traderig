@extends('layouts.app')

@section('title', 'Mis chats')

@section('content')
    
    <section class="py-4">
        <div class="container">
            <h1 class="h3 mb-2">Mis chats</h1>
             <small class="text-body-secondary"> Tus conversaciones con compradores y vendedores.</small>
        </div>
    </section>

    {{-- la lista de las conversaciones --}}
    <section class="pb-5">
        <div class="container">
            @if ($conversations->isEmpty())

                <div class="card">
                    <div class="card-body p-4 text-center">
                        <p class="mb-2">Todavía no tenés conversaciones.</p>
                        <p class="text-body-secondary mb-3">Entrá a una publicación y usá el botón "Consultar al vendedor" para empezar una.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Ver publicaciones</a>
                    </div>
                </div>

            @else

                <div class="row g-4">

                    @foreach ($conversations as $conversation)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <x-avatar :user="$conversation->otherParticipant(auth()->user())" size="md" />
                                            <h2 class="h5 mb-0">{{ $conversation->otherParticipant(auth()->user())->name }}</h2>
                                        </div>
                                    <p class="mb-1">{{ $conversation->listing->title }}</p>
                                    <small class="text-body-secondary mb-3">Última actividad {{ $conversation->updated_at->format('d/m/Y') }}</small>
                                    <a href="{{ route('chats.show', $conversation) }}" class="btn btn-primary mt-auto">Abrir chat</a>
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>

            @endif

        </div>
    </section>
@endsection