@extends('layouts.app')

@section('title', 'Crear cuenta')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6">
                <div class="card">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-4">Crear cuenta</h1>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <x-field name="name" label="Nombre" autocomplete="name" :required="true" />
                            <x-field name="email" type="email" label="Correo electrónico" autocomplete="email" :required="true" />

                            <div class="row">
                                <div class="col-md-6"><x-field name="phone" type="tel" label="Teléfono" autocomplete="tel" /></div>
                                <div class="col-md-6"><x-field name="city" label="Ciudad" autocomplete="address-level2" /></div>
                            </div>

                            <x-field name="password" type="password" label="Contraseña" autocomplete="new-password" hint="Mínimo 8 caracteres." :required="true" />
                            <x-field name="password_confirmation" type="password" label="Repetir contraseña" autocomplete="new-password" :required="true" />

                            <button type="submit" class="btn btn-primary w-100 mt-2">Crear cuenta</button>
                        </form>

                        <p class="mt-4 mb-0 text-body-secondary">
                            ¿Ya tenés cuenta? <a href="{{ route('login') }}">Ingresar</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
