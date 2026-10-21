<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    private function makeListing(User $owner, array $overrides = []): Listing
    {
        $category = Category::firstOrCreate(['slug' => 'procesadores'], ['name' => 'Procesadores']);

        return $owner->listings()->create(array_merge([
            'category_id' => $category->id,
            'title' => 'Ryzen 5 5600',
            'description' => 'Prueba',
            'condition' => 'used',
            'type' => 'sale',
            'price' => 125000,
            'accepts_trade' => true,
        ], $overrides));
    }

    public function test_la_portada_carga_con_y_sin_datos(): void
    {
        $this->get('/')->assertOk();

        $this->makeListing(User::factory()->create());
        $this->get('/')->assertOk()->assertSee('Ryzen 5 5600');
    }

    public function test_el_detalle_de_una_publicacion_activa_se_ve(): void
    {
        $listing = $this->makeListing(User::factory()->create());

        $this->get(route('listings.show', $listing))->assertOk()->assertSee('Ryzen 5 5600');
    }

    public function test_una_publicacion_pausada_solo_la_ve_su_dueno(): void
    {
        $owner = User::factory()->create();
        $listing = $this->makeListing($owner);
        $listing->forceFill(['status' => 'paused'])->save();

        $this->get(route('listings.show', $listing))->assertNotFound();
        $this->actingAs(User::factory()->create())->get(route('listings.show', $listing))->assertNotFound();
        $this->actingAs($owner)->get(route('listings.show', $listing))->assertOk();
    }

    public function test_el_perfil_publico_lista_las_publicaciones(): void
    {
        $user = User::factory()->create();
        $this->makeListing($user);

        $this->get(route('users.show', $user))->assertOk()->assertSee('Ryzen 5 5600');
        $this->get(route('users.show', ['user' => $user, 'filtro' => 'subasta']))
            ->assertOk()->assertDontSee('Ryzen 5 5600');
    }

    public function test_el_perfil_de_una_cuenta_bloqueada_no_existe(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['is_blocked' => true])->save();

        $this->get(route('users.show', $user))->assertNotFound();
    }
}
