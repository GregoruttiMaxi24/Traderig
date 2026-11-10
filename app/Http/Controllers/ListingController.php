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
        $listing->auction?->loadCount('bids');

        $sellerListingsCount = $listing->user->listings()->active()->count();
        $galleryPhotos = $listing->photos
            ->sortByDesc('is_main')
            ->values();

        return view('listings.show', compact('listing', 'sellerListingsCount', 'galleryPhotos'));
    }
}
