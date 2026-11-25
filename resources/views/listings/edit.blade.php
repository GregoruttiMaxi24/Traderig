@extends('layouts.app')

@section('title', 'Editar publicación')

@section('content')
    <div class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <a href="{{ route('listings.show', $listing) }}" class="tr-listing-form__back">Volver a la publicación</a>

                <header class="tr-listing-form__header">

                 <h1>Editar publicación</h1>
                    <p>Cambiá los datos de tu componente.</p>
                </header>

                <form method="POST" action="{{ route('listings.update', $listing) }}" class="tr-listing-form">
                    @csrf

                    <section class="tr-listing-form__section">
                        <h2 class="mb-4">Datos del producto</h2>

                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Nombre del componente</label>

                                    <input type="text" id="title" name="title"
                                        @class(['form-control', 'is-invalid' => $errors->has('title')])
                                        @error('title')
                                        aria-invalid=true
                                        aria-errormessage="error-title"
                                        @enderror
                                        value="{{ old('title', $listing->title) }}"
                                    >

                                    @error('title')
                                        <div class="text-danger" id="error-title">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoría</label>

                                    <select id="category_id" name="category_id"
                                        @class(['form-select', 'is-invalid' => $errors->has('category_id')])
                                        @error('category_id')
                                        aria-invalid=true
                                        aria-errormessage="error-category_id"
                                        @enderror
                                    >
                                        <option value="">Elegí una categoría</option>

                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @selected($category->id == old('category_id', $listing->category_id))
                                            >
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('category_id')
                                        <div class="text-danger" id="error-category_id">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>

                            <textarea id="description" name="description" rows="5"
                                @class(['form-control', 'is-invalid' => $errors->has('description')])
                                @error('description')
                                aria-invalid=true
                                aria-errormessage="error-description"
                                @enderror
                            >{{ old('description', $listing->description) }}</textarea>

                            @error('description')
                                <div class="text-danger" id="error-description">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="seller_notes" class="form-label">Observaciones del vendedor <span class="text-body-secondary">(opcional)</span></label>

                            <textarea id="seller_notes" name="seller_notes" rows="3" class="form-control">{{ old('seller_notes', $listing->seller_notes) }}</textarea>
                        </div>

                        <div class="tr-specification-editor" data-specifications>
                            <div class="tr-specification-editor__header mt-5">
                                <div>
                                    <h3>Especificaciones técnicas</h3>
                                    <p>Agregá al menos una característica. Podés incluir hasta 10.</p>
                                </div>
                                <button type="button" class="btn btn-outline-tr btn-sm" data-specification-add>
                                    + Agregar característica
                                </button>
                            </div>

                            @error('specifications')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                            <div class="tr-specification-editor__list" data-specifications-list data-max="10">
                                @foreach (old('specifications', $specifications) as $index => $specification)
                                    <div class="tr-specification-row" data-specification-row data-index="{{ $index }}">
                                        <div>
                                            <label for="specification-name-{{ $index }}" class="form-label">Característica</label>

                                            <input type="text" id="specification-name-{{ $index }}" name="specifications[{{ $index }}][name]"
                                                @class(['form-control', 'is-invalid' => $errors->has("specifications.$index.name")])
                                                value="{{ $specification['name'] ?? '' }}"
                                            >

                                            @error("specifications.$index.name")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="specification-value-{{ $index }}" class="form-label">Detalle</label>

                                            <input type="text" id="specification-value-{{ $index }}" name="specifications[{{ $index }}][value]"
                                                @class(['form-control', 'is-invalid' => $errors->has("specifications.$index.value")])
                                                value="{{ $specification['value'] ?? '' }}"
                                            >

                                            @error("specifications.$index.value")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="button" class="btn btn-outline-tr tr-specification-row__remove" data-specification-remove>
                                            Quitar
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <template data-specification-template>
                                <div class="tr-specification-row" data-specification-row data-index="__INDEX__">
                                    <div>
                                        <label for="specification-name-__INDEX__" class="form-label">Característica</label>
                                        <input type="text" id="specification-name-__INDEX__" name="specifications[__INDEX__][name]" class="form-control">
                                    </div>
                                    <div>
                                        <label for="specification-value-__INDEX__" class="form-label">Detalle</label>
                                        <input type="text" id="specification-value-__INDEX__" name="specifications[__INDEX__][value]" class="form-control">
                                    </div>
                                    <button type="button" class="btn btn-outline-tr tr-specification-row__remove" data-specification-remove>
                                        Quitar
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Marca <span class="text-body-secondary">(opcional)</span></label>

                                    <input type="text" id="brand" name="brand"
                                        @class(['form-control', 'is-invalid' => $errors->has('brand')])
                                        value="{{ old('brand', $listing->brand) }}"
                                    >

                                    @error('brand')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="condition" class="form-label">Estado</label>

                                    <select id="condition" name="condition"
                                        @class(['form-select', 'is-invalid' => $errors->has('condition')])
                                    >
                                        @foreach ($conditions as $value => $label)
                                            <option value="{{ $value }}"
                                                @selected($value === old('condition', $listing->condition))
                                            >
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('condition')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="form-label d-block">Tipo de publicación</span>
                                    <p class="form-control-plaintext">{{ $listing->type_label }}</p>
                                    <span class="form-text">El tipo no se puede cambiar.</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    @if ($listing->type === 'sale')
                                        <label for="price" class="form-label">Precio</label>

                                        <input type="text" id="price" name="price"
                                            @class(['form-control', 'is-invalid' => $errors->has('price')])
                                            @error('price')
                                            aria-invalid=true
                                            aria-errormessage="error-price"
                                            @enderror
                                            value="{{ old('price', $listing->price) }}"
                                        >

                                        @error('price')
                                            <div class="text-danger" id="error-price">{{ $message }}</div>
                                        @enderror
                                    @else
                                        <span class="form-label d-block">Precio inicial</span>
                                        <p class="form-control-plaintext">{{ \App\Support\Money::format($listing->price) }}</p>
                                        <span class="form-text">El precio de una subasta no se puede modificar.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">Ciudad <span class="text-body-secondary">(opcional)</span></label>

                                    <input type="text" id="city" name="city"
                                        @class(['form-control', 'is-invalid' => $errors->has('city')])
                                        value="{{ old('city', $listing->city) }}"
                                    >

                                    @error('city')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check mb-3">
                                    <input id="accepts_trade" name="accepts_trade" type="checkbox" value="1"
                                        class="form-check-input"
                                        @checked(old('accepts_trade', $listing->accepts_trade))
                                    >
                                    <label for="accepts_trade" class="form-check-label">Acepto propuestas de intercambio</label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="tr-listing-form__actions">
                        <a href="{{ route('listings.show', $listing) }}" class="btn btn-outline-tr">Cancelar</a>
                        <button type="submit" class="btn btn-accent">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/app.js')
@endpush