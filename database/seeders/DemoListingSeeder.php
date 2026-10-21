<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

// Datos de ejemplo para que todo el equipo pueda probar catálogo, subastas e intercambios.
// Se puede correr varias veces sin duplicar (usa firstOrCreate).
class DemoListingSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::where('email', 'usuario@traderig.test')->firstOrFail();
        $admin = User::where('email', 'admin@traderig.test')->firstOrFail();
        $lucia = $this->member('Lucía Gómez', 'lucia@traderig.test', 'Morón');
        $mateo = $this->member('Mateo Ruiz', 'mateo@traderig.test', 'Merlo');

        $items = [
            // [dueño, categoría, título, marca, estado, tipo, precio, acepta trueque, ciudad, descripción]
            [$usuario, 'procesadores', 'Ryzen 5 5600 con cooler original', 'AMD', 'like_new', 'sale', 125000, true, 'Ituzaingó', 'Procesador de 6 núcleos, usado muy poco en una PC de oficina. Incluye cooler y caja.'],
            [$lucia, 'placas-de-video', 'GeForce RTX 3060 12GB Dual', 'ASUS', 'used', 'auction', 260000, false, 'Morón', 'Funciona perfecto, sin minería. Se subasta con precio base y puja mínima de $ 5.000.'],
            [$mateo, 'memorias-ram', 'Memoria RAM DDR4 16GB (2x8) 3200 MHz', 'Corsair', 'used', 'sale', 48000, true, 'Merlo', 'Kit de dos módulos, probado con MemTest sin errores.'],
            [$usuario, 'almacenamiento', 'SSD NVMe 1TB WD Blue', 'Western Digital', 'like_new', 'sale', 72000, false, 'Ituzaingó', 'Salud del disco al 98 %. Ideal para sistema y juegos.'],
            [$lucia, 'fuentes-de-alimentacion', 'Fuente 650W 80+ Bronze', 'EVGA', 'used', 'sale', 55000, true, 'Castelar', 'Cables incluidos, sin ruidos ni coil whine.'],
            [$mateo, 'gabinetes', 'Gabinete gamer con 4 coolers RGB', 'Aerocool', 'new', 'auction', 60000, false, 'Merlo', 'Nuevo en caja, panel lateral de vidrio templado.'],
            [$usuario, 'monitores', 'Monitor 24" 144Hz IPS', 'AOC', 'used', 'sale', 180000, true, 'Haedo', 'Sin píxeles muertos, incluye cable DisplayPort.'],
            [$mateo, 'refrigeracion', 'Cooler CPU torre doble ventilador', 'DeepCool', 'like_new', 'sale', 28000, false, 'Merlo', 'Compatible con AM4 y LGA1700.'],
            [$lucia, 'motherboards', 'Motherboard B450M DS3H', 'Gigabyte', 'used', 'auction', 70000, false, 'Morón', 'Con blindaje trasero. BIOS actualizada para Ryzen serie 5000.'],
            [$usuario, 'teclados-y-mouses', 'Teclado mecánico TKL switches rojos', 'Redragon', 'used', 'sale', 35000, true, 'Merlo', 'Retroiluminado, funciona todo.'],
        ];

        $auctionEnds = [
            'GeForce RTX 3060 12GB Dual' => now()->addDays(2)->addHours(3),
            'Gabinete gamer con 4 coolers RGB' => now()->addHours(5)->addMinutes(40),
            'Motherboard B450M DS3H' => now()->addDay(),
        ];

        foreach ($items as [$owner, $slug, $title, $brand, $condition, $type, $price, $trade, $city, $description]) {
            $listing = $owner->listings()->firstOrCreate(['title' => $title], [
                'category_id' => Category::where('slug', $slug)->value('id'),
                'description' => $description,
                'brand' => $brand,
                'condition' => $condition,
                'type' => $type,
                'price' => $price,
                'accepts_trade' => $trade,
                'city' => $city,
            ]);

            if ($type === 'auction') {
                $listing->auction()->firstOrCreate([], [
                    'starting_price' => $price,
                    'min_increment' => 5000,
                    'current_price' => $price,
                    'ends_at' => $auctionEnds[$title],
                ]);
            }
        }

        $this->bid('GeForce RTX 3060 12GB Dual', [[$usuario, 265000], [$mateo, 270000]]);
        $this->bid('Gabinete gamer con 4 coolers RGB', [[$lucia, 62000]]);
    }

    private function member(string $name, string $email, string $city): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => $name,
            'city' => $city,
            'password' => 'password',
            'role' => User::ROLE_USER,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }

    private function bid(string $title, array $bids): void
    {
        $auction = \App\Models\Listing::where('title', $title)->first()?->auction;

        if (! $auction || $auction->bids()->exists()) {
            return;
        }

        foreach ($bids as [$user, $amount]) {
            Bid::create(['auction_id' => $auction->id, 'user_id' => $user->id, 'amount' => $amount]);
            $auction->update(['current_price' => $amount]);
        }
    }
}
