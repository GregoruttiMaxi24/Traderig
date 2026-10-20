@extends('layouts.app')

@section('title', 'Administración')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-1">Administración</h1>
        <p class="text-body-secondary mb-4">La moderación de publicaciones y usuarios se completa en la Fase 7.</p>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card"><div class="card-body">
                    <div class="tr-price fs-2">{{ $usersCount }}</div>
                    <div class="text-body-secondary">Usuarios</div>
                </div></div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card"><div class="card-body">
                    <div class="tr-price fs-2">{{ $listingsCount }}</div>
                    <div class="text-body-secondary">Publicaciones</div>
                </div></div>
            </div>
        </div>

        <h2 class="h5 mb-3">Últimos usuarios</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Alta</th></tr>
                </thead>
                <tbody>
                    @foreach ($latestUsers as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td class="tr-meta">{{ $u->email }}</td>
                            <td>{{ $u->isAdmin() ? 'Administrador' : 'Usuario' }}</td>
                            <td class="tr-meta">{{ $u->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
