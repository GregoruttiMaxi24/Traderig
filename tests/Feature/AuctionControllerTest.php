<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_auction_state_includes_bid_count_and_current_auction_data(): void
    {
        $seller = User::factory()->create();
        $firstBidder = User::factory()->create();
        $secondBidder = User::factory()->create();
        $category = Category::query()->create([
            'name' => 'Tarjetas gráficas',
            'slug' => 'tarjetas-graficas',
        ]);
        $listing = $seller->listings()->create([
            'category_id' => $category->id,
            'title' => 'Radeon RX 6700 XT',
            'description' => 'Placa de video en buen estado.',
            'condition' => 'used',
            'type' => 'auction',
            'price' => 100000,
            'accepts_trade' => false,
        ]);
        $auction = $listing->auction()->create([
            'starting_price' => 100000,
            'current_price' => 120000,
            'min_increment' => 5000,
            'ends_at' => now()->addDays(2),
        ]);
        $auction->bids()->createMany([
            ['user_id' => $firstBidder->id, 'amount' => 110000],
            ['user_id' => $secondBidder->id, 'amount' => 120000],
        ]);

        $this->get(route('auctions.state', $auction))
            ->assertOk()
            ->assertJsonPath('bid_count', 2)
            ->assertJsonPath('has_bids', true)
            ->assertJsonPath('current_price', 120000)
            ->assertJsonPath('minimum_next_bid', 125000)
            ->assertJsonPath('bids.0.amount', 120000);
    }

    public function test_auction_state_returns_not_found_for_an_inactive_listing(): void
    {
        $seller = User::factory()->create();
        $category = Category::query()->create([
            'name' => 'Tarjetas gráficas',
            'slug' => 'tarjetas-graficas',
        ]);
        $listing = $seller->listings()->create([
            'category_id' => $category->id,
            'title' => 'Radeon RX 6700 XT',
            'description' => 'Placa de video en buen estado.',
            'condition' => 'used',
            'type' => 'auction',
            'price' => 100000,
            'accepts_trade' => false,
        ]);
        $listing->forceFill(['status' => 'paused'])->save();
        $auction = $listing->auction()->create([
            'starting_price' => 100000,
            'current_price' => 100000,
            'min_increment' => 5000,
            'ends_at' => now()->addDays(2),
        ]);

        $this->get(route('auctions.state', $auction))
            ->assertNotFound();
    }
}
