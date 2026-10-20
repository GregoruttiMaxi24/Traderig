<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Procesadores',
            'Placas de video',
            'Motherboards',
            'Memorias RAM',
            'Almacenamiento',
            'Fuentes de alimentación',
            'Gabinetes',
            'Refrigeración',
            'Monitores',
            'Teclados y mouses',
            'Notebooks',
            'Otros',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
