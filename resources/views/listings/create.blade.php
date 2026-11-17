@extends('layouts.app')

@section('title', 'Publicar componente')

@section('content')
    <div class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <a href="{{ route('home') }}" class="tr-listing-form__back">← Volver al inicio</a>
                <header class="tr-listing-form__header">
                    <h1>Publicá tu componente</h1>
                    <p>Agregá fotos y detalles para que otros puedan encontrarlo.</p>
                </header>

                <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="tr-listing-form">
                    @csrf

                    <section class="tr-listing-form__section">
                        <h2>Fotos del componente</h2>
                        <p>Subí de 1 a 5 imágenes JPG, PNG o WebP. Máximo 5 MB cada una.</p>

                        <label for="photos" class="tr-upload">
                            <span class="tr-upload__mark" aria-hidden="true">+</span>
                            <span class="tr-upload__title">Elegir fotos</span>
                            <span class="tr-upload__hint">La primera foto será la portada de la publicación.</span>
                            <input
                                id="photos"
                                name="photos[]"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                required
                                data-photo-input
                                aria-describedby="photos-hint @if ($errors->has('photos') || $errors->has('photos.0')) photos-error @endif"
                                class="@if ($errors->has('photos') || $errors->has('photos.0')) is-invalid @endif"
                            >
                        </label>
                        <div id="photos-hint" class="form-text">Podés seleccionar hasta 5 fotos. En caso de error, elegilas nuevamente.</div>
                        @if ($errors->has('photos') || $errors->has('photos.0'))
                            <div id="photos-error" class="tr-listing-form__error" role="alert">
                                {{ $errors->first('photos') ?: $errors->first('photos.0') }}
                            </div>
                        @endif
                        @foreach ($errors->get('photos.*') as $photoErrors)
                            @foreach ($photoErrors as $photoError)
                                <div class="tr-listing-form__error" role="alert">{{ $photoError }}</div>
                            @endforeach
                        @endforeach
                        <div class="tr-upload__previews" data-photo-preview aria-live="polite"></div>
                    </section>

                    <section class="tr-listing-form__section">
                        <h2>Datos del producto</h2>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Nombre del componente</label>
                                    <input id="title" name="title" type="text" maxlength="150" value="{{ old('title') }}"
                                        class="form-control @error('title') is-invalid @enderror" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoría</label>
                                    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">Elegí una categoría</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea id="description" name="description" rows="5" maxlength="5000"
                                class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="seller_notes" class="form-label">Observaciones del vendedor <span class="text-body-secondary">(opcional)</span></label>
                            <textarea id="seller_notes" name="seller_notes" rows="3" maxlength="1200"
                                class="form-control @error('seller_notes') is-invalid @enderror">{{ old('seller_notes') }}</textarea>
                            @error('seller_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @php
                            $specifications = old('specifications');
                            if (! is_array($specifications) || $specifications === []) {
                                $specifications = [['name' => '', 'value' => '']];
                            }
                        @endphp

                        <div class="tr-specification-editor" data-specifications>
                            <div class="tr-specification-editor__header">
                                <div>
                                    <h3>Especificaciones técnicas</h3>
                                    <p>Agregá al menos una característica. Podés incluir hasta 10.</p>
                                </div>
                                <button type="button" class="btn btn-outline-tr btn-sm" data-specification-add>
                                    Agregar característica
                                </button>
                            </div>

                            @error('specifications')
                                <div class="tr-listing-form__error" role="alert">{{ $message }}</div>
                            @enderror
                            @error('specifications.*')
                                <div class="tr-listing-form__error" role="alert">{{ $message }}</div>
                            @enderror

                            <div class="tr-specification-editor__list" data-specifications-list data-max="10">
                                @foreach ($specifications as $index => $specification)
                                    @php
                                        $specification = is_array($specification) ? $specification : [];
                                    @endphp
                                    <div class="tr-specification-row" data-specification-row data-index="{{ $index }}">
                                        <div>
                                            <label for="specification-name-{{ $index }}" class="form-label">Característica</label>
                                            <input
                                                id="specification-name-{{ $index }}"
                                                name="specifications[{{ $index }}][name]"
                                                type="text"
                                                maxlength="80"
                                                value="{{ $specification['name'] ?? '' }}"
                                                class="form-control @error("specifications.$index.name") is-invalid @enderror"
                                                required
                                            >
                                            @error("specifications.$index.name")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="specification-value-{{ $index }}" class="form-label">Detalle</label>
                                            <input
                                                id="specification-value-{{ $index }}"
                                                name="specifications[{{ $index }}][value]"
                                                type="text"
                                                maxlength="200"
                                                value="{{ $specification['value'] ?? '' }}"
                                                class="form-control @error("specifications.$index.value") is-invalid @enderror"
                                                required
                                            >
                                            @error("specifications.$index.value")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button
                                            type="button"
                                            class="btn btn-outline-tr tr-specification-row__remove"
                                            data-specification-remove
                                            aria-label="Quitar especificación {{ $index + 1 }}"
                                            @disabled(count($specifications) === 1)
                                        >
                                            Quitar
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <template data-specification-template>
                                <div class="tr-specification-row" data-specification-row data-index="__INDEX__">
                                    <div>
                                        <label for="specification-name-__INDEX__" class="form-label">Característica</label>
                                        <input
                                            id="specification-name-__INDEX__"
                                            name="specifications[__INDEX__][name]"
                                            type="text"
                                            maxlength="80"
                                            class="form-control"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label for="specification-value-__INDEX__" class="form-label">Detalle</label>
                                        <input
                                            id="specification-value-__INDEX__"
                                            name="specifications[__INDEX__][value]"
                                            type="text"
                                            maxlength="200"
                                            class="form-control"
                                            required
                                        >
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-outline-tr tr-specification-row__remove"
                                        data-specification-remove
                                        aria-label="Quitar especificación"
                                    >
                                        Quitar
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Marca <span class="text-body-secondary">(opcional)</span></label>
                                    <input id="brand" name="brand" type="text" maxlength="80" value="{{ old('brand') }}"
                                        class="form-control @error('brand') is-invalid @enderror">
                                    @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="condition" class="form-label">Estado</label>
                                    <select id="condition" name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                                        @foreach ($conditions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('condition', 'used') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipo de publicación</label>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        @foreach ($types as $value => $label)
                                            <option value="{{ $value }}" @selected(old('type', 'sale') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Precio o precio inicial</label>
                                    <input id="price" name="price" type="number" min="1" step="1" value="{{ old('price') }}"
                                        class="form-control @error('price') is-invalid @enderror" required>
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="auction_ends_at" class="form-label">Cierre de subasta <span class="text-body-secondary">(solo subasta)</span></label>
                                    <input id="auction_ends_at" name="auction_ends_at" type="datetime-local" value="{{ old('auction_ends_at') }}"
                                        class="form-control @error('auction_ends_at') is-invalid @enderror">
                                    @error('auction_ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="auction_min_increment" class="form-label">Aumento mínimo <span class="text-body-secondary">(solo subasta)</span></label>
                                    <input id="auction_min_increment" name="auction_min_increment" type="number" min="1" step="1"
                                        value="{{ old('auction_min_increment', 5000) }}"
                                        class="form-control @error('auction_min_increment') is-invalid @enderror">
                                    @error('auction_min_increment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">Ciudad <span class="text-body-secondary">(opcional)</span></label>
                                    <input id="city" name="city" type="text" maxlength="100" value="{{ old('city', auth()->user()->city) }}"
                                        class="form-control @error('city') is-invalid @enderror">
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check mb-3">
                                    <input id="accepts_trade" name="accepts_trade" type="checkbox" value="1"
                                        class="form-check-input @error('accepts_trade') is-invalid @enderror" @checked(old('accepts_trade'))>
                                    <label for="accepts_trade" class="form-check-label">Acepto propuestas de intercambio</label>
                                    @error('accepts_trade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="tr-listing-form__actions">
                        <a href="{{ route('home') }}" class="btn btn-outline-tr">Cancelar</a>
                        <button type="submit" class="btn btn-accent">Publicar componente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/app.js')
@endpush
