@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="display-6">No encontramos esta página</h1>
        <p class="text-body-secondary mt-3">Puede que el enlace esté mal o que ya no exista.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Volver al inicio</a>
    </div>
@endsection
