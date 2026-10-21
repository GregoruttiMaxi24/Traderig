@extends('layouts.app')

@section('title', $user->name)

@section('content')
    @php
        $pills = [
            null => 'Todas',
            'venta' => 'En venta',
            'subasta' => 'Subastas',
            'trueque' => 'Aceptan intercambio',
        ];
    @endphp

    <div class="container py-4">
        <div class="tr-cover" role="img" aria-label="Portada del perfil"></div>

        <div class="tr-profile-head">
            <x-avatar :user="$user" size="xl" class="tr-profile-avatar" />
            <div class="flex-grow-1">
                <h1 class="tr-title h2 mb-1">{{ $user->name }}</h1>
                <p class="tr-meta text-body-secondary mb-0">
                    {{ $user->city ?: 'Ubicación sin indicar' }} · Miembro desde {{ $user->created_at->format('m/Y') }}
                </p>
            </div>
            <dl class="tr-stats">
                <div><dt>Publicaciones</dt><dd>{{ $activeCount }}</dd></div>
                <div><dt>Subastas ganadas</dt><dd>{{ $wonCount }}</dd></div>
            </dl>
        </div>

        <hr class="tr-divider my-4">

        <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Filtrar publicaciones">
            @foreach ($pills as $key => $label)
                <a class="tr-pill {{ ($filter ?? '') === $key ? 'active' : '' }}"
                   href="{{ route('users.show', ['user' => $user] + ($key ? ['filtro' => $key] : [])) }}"
                   @if (($filter ?? '') === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="row g-4">
            @forelse ($listings as $listing)
                <div class="col-md-6 col-lg-4"><x-listing-card :listing="$listing" /></div>
            @empty
                <p class="text-body-secondary">No hay publicaciones para mostrar con este filtro.</p>
            @endforelse
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $listings->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
