<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        return $admin;
    }

    public function test_el_usuario_se_registra_y_queda_con_rol_user(): void
    {
        $response = $this->post('/registro', [
            'name' => 'Ana Prueba',
            'email' => 'ana@example.com',
            'password' => 'clave-segura-1',
            'password_confirmation' => 'clave-segura-1',
            'role' => 'admin', // intento de hacerse admin desde el formulario
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertSame('user', User::where('email', 'ana@example.com')->first()->role);
    }

    public function test_el_registro_rechaza_un_correo_repetido(): void
    {
        $user = User::factory()->create();

        $this->post('/registro', [
            'name' => 'Otra Persona',
            'email' => $user->email,
            'password' => 'clave-segura-1',
            'password_confirmation' => 'clave-segura-1',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_el_usuario_puede_ingresar_con_sus_datos(): void
    {
        $user = User::factory()->create();

        $this->post('/ingresar', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_no_ingresa_con_contrasena_incorrecta(): void
    {
        $user = User::factory()->create();

        $this->post('/ingresar', ['email' => $user->email, 'password' => 'otra-cosa'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_una_cuenta_bloqueada_no_puede_ingresar(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['is_blocked' => true])->save();

        $this->post('/ingresar', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_el_usuario_cierra_sesion(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/salir')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_el_perfil_exige_sesion(): void
    {
        $this->get('/perfil')->assertRedirect(route('login'));
    }

    public function test_el_usuario_actualiza_sus_datos(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/perfil', [
            'name' => 'Nombre Nuevo',
            'email' => $user->email,
            'phone' => '1155551234',
            'city' => 'Ituzaingó',
        ])->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nombre Nuevo', 'city' => 'Ituzaingó']);
    }

    public function test_el_usuario_cambia_su_contrasena(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/perfil/password', [
            'current_password' => 'password',
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('nueva-clave-123', $user->fresh()->password));
    }

    public function test_un_usuario_comun_no_entra_a_administracion(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_un_visitante_es_enviado_a_ingresar_desde_administracion(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_el_administrador_entra_a_administracion(): void
    {
        $this->actingAs($this->makeAdmin())->get('/admin')->assertOk();
    }
}
