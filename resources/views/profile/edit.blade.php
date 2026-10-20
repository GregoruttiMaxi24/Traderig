@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <h1 class="h3 mb-1">Mi perfil</h1>
                <p class="tr-meta text-body-secondary mb-4">
                    Rol: {{ $user->isAdmin() ? 'administrador' : 'usuario' }} · Miembro desde {{ $user->created_at->format('d/m/Y') }}
                </p>

                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Mis datos</h2>
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')
                            <x-field name="name" label="Nombre" :value="$user->name" autocomplete="name" :required="true" />
                            <x-field name="email" type="email" label="Correo electrónico" :value="$user->email" autocomplete="email" :required="true" />
                            <div class="row">
                                <div class="col-md-6"><x-field name="phone" type="tel" label="Teléfono" :value="$user->phone" autocomplete="tel" /></div>
                                <div class="col-md-6"><x-field name="city" label="Ciudad" :value="$user->city" autocomplete="address-level2" /></div>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar datos</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Cambiar contraseña</h2>
                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PUT')
                            <x-field name="current_password" type="password" bag="password" label="Contraseña actual" autocomplete="current-password" :required="true" />
                            <x-field name="password" type="password" bag="password" label="Contraseña nueva" autocomplete="new-password" hint="Mínimo 8 caracteres." :required="true" />
                            <x-field name="password_confirmation" type="password" bag="password" label="Repetir contraseña nueva" autocomplete="new-password" :required="true" />
                            <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
