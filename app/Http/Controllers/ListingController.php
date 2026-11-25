<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ListingController extends Controller
{
    public function create(): View
    {
        return view('listings.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'conditions' => Listing::CONDITIONS,
            'types' => Listing::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'brand' => ['nullable', 'string', 'max:80'],
            'seller_notes' => ['nullable', 'string', 'max:1200'],
            'condition' => ['required', Rule::in(array_keys(Listing::CONDITIONS))],
            'type' => ['required', Rule::in(array_keys(Listing::TYPES))],
            'price' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'accepts_trade' => ['sometimes', 'boolean'],
            'city' => ['nullable', 'string', 'max:100'],
            'auction_ends_at' => ['exclude_unless:type,auction', 'required', 'date', 'after:now'],
            'auction_min_increment' => ['exclude_unless:type,auction', 'required', 'integer', 'min:1', 'max:4294967295'],
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
            'specifications' => ['required', 'array', 'min:1', 'max:10'],
            'specifications.*' => ['required', 'array:name,value'],
            'specifications.*.name' => ['required', 'string', 'max:80'],
            'specifications.*.value' => ['required', 'string', 'max:200'],
        ], [
            'auction_ends_at.after' => 'La fecha de cierre debe ser posterior al momento actual.',
            'auction_ends_at.required' => 'Indicá cuándo termina la subasta.',
            'auction_min_increment.required' => 'Indicá el aumento mínimo de cada puja.',
            'photos.required' => 'Subí al menos una foto del componente.',
            'photos.min' => 'Subí al menos una foto del componente.',
            'photos.max' => 'Podés subir hasta 5 fotos por publicación.',
            'photos.*.image' => 'Cada archivo debe ser una imagen.',
            'photos.*.mimes' => 'Las fotos deben estar en formato JPG, PNG o WebP.',
            'photos.*.extensions' => 'Usá archivos con extensión JPG, PNG o WebP.',
            'photos.*.max' => 'Cada foto puede pesar hasta 5 MB.',
            'seller_notes.max' => 'Las observaciones pueden tener hasta 1200 caracteres.',
            'specifications.required' => 'Agregá al menos una especificación del componente.',
            'specifications.min' => 'Agregá al menos una especificación del componente.',
            'specifications.max' => 'Podés agregar hasta 10 especificaciones.',
            'specifications.*.name.required' => 'Indicá el nombre de cada especificación.',
            'specifications.*.name.max' => 'El nombre de una especificación puede tener hasta 80 caracteres.',
            'specifications.*.value.required' => 'Indicá el valor de cada especificación.',
            'specifications.*.value.max' => 'El valor de una especificación puede tener hasta 200 caracteres.',
        ]);

        $listingAttributes = Arr::only($data, [
            'category_id',
            'title',
            'description',
            'brand',
            'seller_notes',
            'condition',
            'type',
            'price',
            'city',
        ]);
        $listingAttributes['specifications'] = array_map(
            static fn (array $specification): array => [
                'name' => trim($specification['name']),
                'value' => trim($specification['value']),
            ],
            $data['specifications'],
        );
        $listingAttributes['accepts_trade'] = $request->boolean('accepts_trade');
        $storedPhotoPaths = [];

        try {
            $listing = DB::transaction(function () use ($data, $listingAttributes, $request, &$storedPhotoPaths): Listing {
                $listing = $request->user()->listings()->create($listingAttributes);

                if ($listing->isAuction()) {
                    $listing->auction()->create([
                        'starting_price' => $listing->price,
                        'current_price' => $listing->price,
                        'min_increment' => $data['auction_min_increment'],
                        'ends_at' => $data['auction_ends_at'],
                    ]);
                }

                foreach ($data['photos'] as $index => $photo) {
                    $path = Storage::disk('public')->putFile('listings', $photo);

                    if (! is_string($path) || $path === '') {
                        throw new RuntimeException('No se pudo guardar una foto de la publicación.');
                    }

                    $storedPhotoPaths[] = $path;

                    $listing->photos()->create([
                        'path' => $path,
                        'is_main' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }

                return $listing;
            });
        } catch (Throwable $exception) {
            if ($storedPhotoPaths !== [] && ! Storage::disk('public')->delete($storedPhotoPaths)) {
                report(new RuntimeException('No se pudieron eliminar fotos tras cancelar una publicación.', previous: $exception));
            }

            throw $exception;
        }

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'Publicaste tu componente.');
    }

    public function show(Request $request, Listing $listing): View
    {
        $viewer = $request->user();

        // Solo se ven las publicaciones activas; el dueño y los admins ven todas.
        $canSee = $listing->status === 'active'
            || ($viewer && ($viewer->id === $listing->user_id || $viewer->isAdmin()));

        abort_unless($canSee, 404);

        $listing->load(['category', 'user', 'photos', 'mainPhoto', 'auction']);

     //solo las publicaciones de tipo subasta tienen subasta.
        if ($listing->auction !== null) {
            $listing->auction->closeIfExpired();
            $listing->auction->loadCount('bids');
        }

        $sellerListingsCount = $listing->user->listings()->active()->count();
        $galleryPhotos = $listing->photos
            ->sortByDesc('is_main')
            ->values();

        return view('listings.show', compact('listing', 'sellerListingsCount', 'galleryPhotos'));
    }


    /**
     * Muestra la tabla con las publicaciones del usuario con sesión iniciada,
     * la publicación más nueva queda arriba.
     * 
     */
    public function myListings(Request $request)
    {
        $listings = $request->user()->listings()->with('category')->latest()->get();

        return view('listings.mine', compact('listings'));
    }


    /**
     * Muestra el formulario de edición de una publicación
     * 
     */
 
    public function edit(Request $request, Listing $listing)
    {
        if ($listing->user_id !== $request->user()->id) {
            abort(403);
        }

        
        $specifications = $listing->specifications; // Si la publicación no tiene especificaciones cargadas se muestra una fila vacía

        if ($specifications === null || $specifications === []) {
            $specifications = [['name' => '', 'value' => '']];
        }

        return view('listings.edit', [
            'listing' => $listing,
            'specifications' => $specifications,
            'categories' => Category::query()->orderBy('name')->get(),
            'conditions' => Listing::CONDITIONS,
        ]);
    }

    /**
     * Valida los datos recibidos, actualiza la publicación y vuelve a su ficha
     * con un mensaje de confirmación.
     */
    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id !== $request->user()->id) {
            abort(403);
        }

        $rules = [
            'title' => ['required', 'min:3', 'max:150'],
            'description' => ['required'],
            'category_id' => ['required', 'exists:categories,id'],
            'condition' => ['required'],
            'brand' => ['nullable', 'max:80'],
            'city' => ['nullable', 'max:100'],
            'specifications' => ['required', 'array', 'max:10'],
            'specifications.*.name' => ['required'],
            'specifications.*.value' => ['required'],
        ];

        //El precio que solo se edite en las ventas
        if ($listing->type === 'sale') {
            $rules['price'] = ['required', 'numeric', 'min:1'];
        }

        $request->validate($rules, [
            'title.required' => 'El nombre del componente es obligatorio.',
            'title.min' => 'El nombre debe tener al menos 3 caracteres.',
            'title.max' => 'El nombre puede tener hasta 150 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'category_id.required' => 'Elegí una categoría.',
            'category_id.exists' => 'La categoría elegida no es válida.',
            'condition.required' => 'Elegí el estado del componente.',
            'brand.max' => 'La marca puede tener hasta 80 caracteres.',
            'city.max' => 'La ciudad puede tener hasta 100 caracteres.',
            'specifications.required' => 'Agregá al menos una especificación del componente.',
            'specifications.max' => 'Podés agregar hasta 10 especificaciones.',
            'specifications.*.name.required' => 'Indicá el nombre de cada especificación.',
            'specifications.*.value.required' => 'Indicá el valor de cada especificación.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.min' => 'El precio debe ser mayor a 0.',
        ]);

        if (!array_key_exists($request->input('condition'), Listing::CONDITIONS)) {
            return back()->withInput()->with('error', 'El estado elegido no es válido.');
        }

        $data = $request->only([
            'category_id',
            'title',
            'description',
            'brand',
            'seller_notes',
            'condition',
            'city',
            'specifications',
        ]);

        $data['accepts_trade'] = $request->has('accepts_trade');

        if ($listing->type === 'sale') {
            $data['price'] = (int) $request->input('price');
        }

        $listing->update($data);

        return redirect()->route('listings.show', $listing)
            ->with('success', 'Guardamos los cambios de tu publicación.');
    }
}
