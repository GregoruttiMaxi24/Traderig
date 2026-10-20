@extends('layouts.app')

@section('title', 'Sin permiso')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="display-6">No tenés permiso para entrar acá</h1>
        <p class="text-body-secondary mt-3">Esta sección es solo para administradores.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Volver al inicio</a>
    </div>
@endsection
