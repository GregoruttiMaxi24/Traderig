@extends('layouts.app')

@section('title', 'Ingresar')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-4">Ingresar</h1>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <x-field name="email" type="email" label="Correo electrónico" autocomplete="email" :required="true" />
                            <x-field name="password" type="password" label="Contraseña" autocomplete="current-password" :required="true" />

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                                <label class="form-check-label" for="remember">Mantener la sesión abierta</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>

                        <p class="mt-4 mb-0 text-body-secondary">
                            ¿Todavía no tenés cuenta? <a href="{{ route('register') }}">Crear cuenta</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
