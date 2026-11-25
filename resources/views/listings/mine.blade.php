@extends('layouts.app')

@section('title', 'Mis publicaciones')

@section('content')

    <section class="py-5">
        <div class="container">

            <h1 class="h3 mb-2">Mis publicaciones</h1>
            <p class="text-body-secondary mb-4">Administrá los componentes que publicaste.</p>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead>
                        <tr>
                            <th scope="col">Título</th>
                            <th scope="col">Categoría</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($listings as $listing)
                            <tr>
                                <td>{{ $listing->title }}</td>
                                <td>{{ $listing->category->name }}</td>
                                <td>{{ $listing->type_label }}</td>
                                <td>{{ \App\Models\Listing::STATUSES[$listing->status] }}</td>
                                <td>{{ \App\Support\Money::format($listing->price) }}</td>
                                <td>{{ $listing->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('listings.show', $listing) }}" class="btn btn-outline-tr btn-sm">Ver</a>
                                        <a href="{{ route('listings.edit', $listing) }}" class="btn btn-primary btn-sm">Editar</a>
                                    </div>
                                </td>
                                
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-body-secondary">Todavía no publicaste ningún componente.</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
             </div>

                <a href="{{ route('listings.create') }}" class="btn btn-primary my-4 w-100">Publicar componente</a>
        </div>
    </section>

@endsection