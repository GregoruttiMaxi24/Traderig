<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
        ]);

        // Publicaciones de ejemplo: solo en desarrollo, nunca en producción.
        if (app()->environment('local')) {
            $this->call(DemoListingSeeder::class);
        }
    }
}
