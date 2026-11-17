@extends('layouts.app')

@section('title', 'Chat')

@section('content')

    {{-- encabezado --}}
    <section class="py-4">
        <div class="container">
            <a href="{{ route('chats.index') }}" class="tr-meta"> Volver a mis chats</a>

            <h1 class="h3 mt-2 mb-1">Chat con {{ $conversation->otherParticipant(auth()->user())->name }}</h1>
            
            <p class="mb-0">
                Sobre:
                <a href="{{ route('listings.show', $conversation->listing) }}">{{ $conversation->listing->title }}</a>
            </p>
        </div>
    </section>

    {{-- panel del chat -- chicos: acá se monta Vue --}}
    <section class="pb-5">
        <div class="container">
            <div class="card">
                <div class="card-body p-4">
                    <div
                        id="chat-box"
                        data-messages-url="{{ route('chats.messages', $conversation) }}"
                        data-send-url="{{ route('chats.send', $conversation) }}"
                    >
                        <p class="text-body-secondary mb-0">El chat se está cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection