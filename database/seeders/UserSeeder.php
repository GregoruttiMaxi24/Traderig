<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    // Usuarios SOLO para desarrollo. Cambiar o borrar antes de publicar el sitio.
    public function run(): void
    {
        $this->makeUser('Admin TRADERIG', 'admin@traderig.test', User::ROLE_ADMIN);
        $this->makeUser('Usuario de prueba', 'usuario@traderig.test', User::ROLE_USER);
    }

    private function makeUser(string $name, string $email, string $role): void
    {
        $user = User::firstOrNew(['email' => $email]);

        // forceFill porque "role" no es asignable en masa.
        $user->forceFill([
            'name' => $name,
            'password' => 'password',
            'role' => $role,
            'email_verified_at' => now(),
        ])->save();
    }
}
