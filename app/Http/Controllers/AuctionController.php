<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Support\Money;
use Illuminate\Http\JsonResponse;

class AuctionController extends Controller
{
    /**
     * Devuelve en formato JSON el estado actual de una subasta.
     *
     * Lo usa la pantalla de la subasta para actualizarse sola: precio actual,
     * monto mínimo de la próxima puja, si sigue abierta y la lista de pujas.
     */
    public function state(Auction $auction): JsonResponse
    {
        // Cargar la publicación y las pujas (con su usuario) en pocas consultas.
        $auction->load(['listing', 'bids.user']);

        // Si la publicación no está activa, la subasta no se muestra
        abort_if($auction->listing->status !== 'active', 404);

        $minimum = $auction->minimumNextBid();

        return response()->json([
            'is_open' => $auction->isOpen(),
            'ends_at' => $auction->ends_at->toIso8601String(),
            'has_bids' => $auction->bids->isNotEmpty(),
            'bid_count' => $auction->bids->count(),
            'current_price' => $auction->current_price,
            'current_price_label' => Money::format($auction->current_price),
            'minimum_next_bid' => $minimum,
            'minimum_next_bid_label' => Money::format($minimum),
            'bids' => $auction->bids->map(fn ($bid) => [
                'id' => $bid->id,
                'user' => $bid->user->name,
                'amount' => $bid->amount,
                'amount_label' => Money::format($bid->amount),
                'created_at_human' => $bid->created_at->diffForHumans(),
            ])->values(),
        ]);
    }
}
