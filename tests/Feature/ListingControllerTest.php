<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_before_opening_or_saving_a_listing(): void
    {
        $category = $this->createCategory();

        $this->get(route('listings.create'))->assertRedirectToRoute('login');

        $this->post(route('listings.store'), $this->listingPayload($category))
            ->assertRedirectToRoute('login');

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_authenticated_user_can_open_the_listing_creation_form(): void
    {
        $this->createCategory();

        $this->actingAs(User::factory()->create())
            ->get(route('listings.create'))
            ->assertOk()
            ->assertViewIs('listings.create')
            ->assertSee('Publicá tu componente')
            ->assertSee('photos[]', false)
            ->assertSee('specifications[0][name]', false);
    }

    public function test_authenticated_user_can_create_a_sale_listing_with_five_or_fewer_photos(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = $this->createCategory();
        $payload = $this->listingPayload($category, [
            'photos' => [
                UploadedFile::fake()->image('component-front.jpg'),
                UploadedFile::fake()->image('component-back.webp'),
            ],
            'seller_notes' => 'Incluye la caja original.',
            'specifications' => [
                ['name' => 'Memoria', 'value' => '12 GB GDDR6'],
                ['name' => 'Interfaz', 'value' => 'PCI Express 4.0 x16'],
            ],
            'user_id' => User::factory()->create()->id,
            'status' => 'blocked',
        ]);

        $response = $this->actingAs($user)->post(route('listings.store'), $payload);

        $listing = Listing::query()->where('title', 'Ryzen 5 5600')->firstOrFail();
        $response->assertRedirectToRoute('listings.show', $listing);
        $this->assertSame($user->id, $listing->user_id);
        $this->assertSame('active', $listing->status);
        $this->assertSame('Incluye la caja original.', $listing->seller_notes);
        $this->assertSame([
            ['name' => 'Memoria', 'value' => '12 GB GDDR6'],
            ['name' => 'Interfaz', 'value' => 'PCI Express 4.0 x16'],
        ], $listing->specifications);
        $this->assertDatabaseCount('listing_photos', 2);

        $listing->load('photos');
        $this->assertTrue($listing->photos->first()->is_main);
        Storage::disk('public')->assertExists($listing->photos->first()->path);
        Storage::disk('public')->assertExists($listing->photos->last()->path);
    }

    public function test_listing_creation_requires_at_least_one_photo(): void
    {
        $user = User::factory()->create();
        $payload = $this->listingPayload($this->createCategory(), ['photos' => []]);

        $this->actingAs($user)
            ->from(route('listings.create'))
            ->post(route('listings.store'), $payload)
            ->assertRedirectToRoute('listings.create')
            ->assertSessionHasErrors([
                'photos' => 'Subí al menos una foto del componente.',
            ]);

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_listing_creation_rejects_specifications_without_a_value(): void
    {
        $user = User::factory()->create();
        $payload = $this->listingPayload($this->createCategory(), [
            'specifications' => [['name' => 'Memoria', 'value' => '']],
        ]);

        $this->actingAs($user)
            ->from(route('listings.create'))
            ->post(route('listings.store'), $payload)
            ->assertRedirectToRoute('listings.create')
            ->assertSessionHasErrors([
                'specifications.0.value' => 'Indicá el valor de cada especificación.',
            ]);

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_listing_creation_rejects_more_than_ten_specifications(): void
    {
        $user = User::factory()->create();
        $specifications = [];

        foreach (range(1, 11) as $number) {
            $specifications[] = ['name' => "Detalle {$number}", 'value' => "Valor {$number}"];
        }

        $this->actingAs($user)
            ->from(route('listings.create'))
            ->post(route('listings.store'), $this->listingPayload($this->createCategory(), [
                'specifications' => $specifications,
            ]))
            ->assertRedirectToRoute('listings.create')
            ->assertSessionHasErrors([
                'specifications' => 'Podés agregar hasta 10 especificaciones.',
            ]);

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_listing_creation_rejects_more_than_five_photos(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $photos = [];

        foreach (range(1, 6) as $number) {
            $photos[] = UploadedFile::fake()->image("component-{$number}.jpg");
        }

        $this->actingAs($user)
            ->from(route('listings.create'))
            ->post(route('listings.store'), $this->listingPayload($this->createCategory(), ['photos' => $photos]))
            ->assertRedirectToRoute('listings.create')
            ->assertSessionHasErrors([
                'photos' => 'Podés subir hasta 5 fotos por publicación.',
            ]);

        $this->assertDatabaseCount('listings', 0);
        Storage::disk('public')->assertDirectoryEmpty('listings');
    }

    public function test_listing_creation_rejects_files_that_are_not_images(): void
    {
        $user = User::factory()->create();
        $payload = $this->listingPayload($this->createCategory(), [
            'photos' => [UploadedFile::fake()->createWithContent('script.txt', '<script>alert(1)</script>')],
        ]);

        $this->actingAs($user)
            ->from(route('listings.create'))
            ->post(route('listings.store'), $payload)
            ->assertRedirectToRoute('listings.create')
            ->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_listing_creation_rejects_image_extensions_outside_the_allowed_formats(): void
    {
        $user = User::factory()->create();
        $payload = $this->listingPayload($this->createCategory(), [
            'photos' => [UploadedFile::fake()->image('component.gif')],
        ]);

        $this->actingAs($user)
            ->from(route('listings.create'))
            ->post(route('listings.store'), $payload)
            ->assertRedirectToRoute('listings.create')
            ->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_listing_creation_rejects_photos_larger_than_five_megabytes(): void
    {
        $user = User::factory()->create();
        $payload = $this->listingPayload($this->createCategory(), [
            'photos' => [UploadedFile::fake()->image('component.jpg')->size(5121)],
        ]);

        $this->actingAs($user)
            ->from(route('listings.create'))
            ->post(route('listings.store'), $payload)
            ->assertRedirectToRoute('listings.create')
            ->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_authenticated_user_can_create_an_auction_with_a_future_close_time(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $payload = $this->listingPayload($this->createCategory(), [
            'type' => 'auction',
            'auction_ends_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'auction_min_increment' => 5000,
        ]);

        $response = $this->actingAs($user)->post(route('listings.store'), $payload);

        $listing = Listing::query()->where('title', 'Ryzen 5 5600')->firstOrFail();
        $response->assertRedirectToRoute('listings.show', $listing);
        $this->assertDatabaseHas('auctions', [
            'listing_id' => $listing->id,
            'starting_price' => 125000,
            'current_price' => 125000,
            'min_increment' => 5000,
        ]);

        $this->get(route('listings.show', $listing))
            ->assertOk()
            ->assertSee('Subasta')
            ->assertSee('id="auction-panel"', false);
    }

    public function test_listing_detail_renders_photo_gallery_thumbnails(): void
    {
        $listing = $this->createListing(User::factory()->create());
        $listing->photos()->createMany([
            ['path' => 'listings/front.jpg', 'is_main' => true, 'sort_order' => 0],
            ['path' => 'listings/back.jpg', 'is_main' => false, 'sort_order' => 1],
        ]);

        $this->get(route('listings.show', $listing))
            ->assertOk()
            ->assertSee('Foto 1')
            ->assertSee('Ver foto 2')
            ->assertSee('Descripción del producto')
            ->assertSee('Especificaciones')
            ->assertSee('Observaciones del vendedor')
            ->assertSee('Incluye la caja original.')
            ->assertSee('12 GB GDDR6')
            ->assertSee('1 / 2')
            ->assertSee('Información del vendedor')
            ->assertSee('storage/listings/front.jpg')
            ->assertSee('storage/listings/back.jpg');
    }

    private function createCategory(): Category
    {
        return Category::query()->create([
            'name' => 'Procesadores',
            'slug' => 'procesadores',
        ]);
    }

    private function createListing(User $user): Listing
    {
        return $user->listings()->create([
            'category_id' => $this->createCategory()->id,
            'title' => 'Ryzen 5 5600',
            'description' => 'Procesador en buen estado.',
            'brand' => 'AMD',
            'seller_notes' => 'Incluye la caja original.',
            'specifications' => [
                ['name' => 'Memoria', 'value' => '12 GB GDDR6'],
            ],
            'condition' => 'used',
            'type' => 'sale',
            'price' => 125000,
            'accepts_trade' => false,
        ]);
    }

    private function listingPayload(Category $category, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $category->id,
            'title' => 'Ryzen 5 5600',
            'description' => 'Procesador en buen estado.',
            'brand' => 'AMD',
            'specifications' => [
                ['name' => 'Memoria', 'value' => '12 GB GDDR6'],
            ],
            'condition' => 'used',
            'type' => 'sale',
            'price' => 125000,
            'city' => 'Córdoba',
            'photos' => [UploadedFile::fake()->image('component.jpg')],
        ], $overrides);
    }
}
