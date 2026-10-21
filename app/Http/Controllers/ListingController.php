<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Por ahora solo la página de detalle. El resto del CRUD llega en la Fase 4.
class ListingController extends Controller
{
    public function show(Request $request, Listing $listing): View
    {
        $viewer = $request->user();

        // Solo se ven las publicaciones activas; el dueño y los admins ven todas.
        $canSee = $listing->status === 'active'
            || ($viewer && ($viewer->id === $listing->user_id || $viewer->isAdmin()));

        abort_unless($canSee, 404);

        $listing->load(['category', 'user', 'photos', 'auction']);

        $bids = $listing->auction
            ? $listing->auction->bids()->with('user')->take(10)->get()
            : collect();

        $sellerListingsCount = $listing->user->listings()->active()->count();

        return view('listings.show', compact('listing', 'bids', 'sellerListingsCount'));
    }
}
