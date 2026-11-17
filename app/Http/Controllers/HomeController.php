<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $withRelations = ['user', 'category', 'mainPhoto', 'auction'];

        $featured = Listing::active()
            ->where('type', 'sale')
            ->with($withRelations)
            ->latest()
            ->take(4)
            ->get();

        $trades = Listing::active()
            ->where('type', 'sale')
            ->where('accepts_trade', true)
            ->with($withRelations)
            ->latest()
            ->take(2)
            ->get();

        // Subastas abiertas, las que cierran primero van primero.
        $auctions = Listing::active()
            ->where('type', 'auction')
            ->whereHas('auction', fn ($q) => $q->where('status', 'open')->where('ends_at', '>', now()))
            ->with($withRelations)
            ->get()
            ->sortBy(fn (Listing $listing) => $listing->auction->ends_at)
            ->take(3);

        $categories = Category::withCount(['listings as active_count' => fn ($q) => $q->active()])
            ->orderByDesc('active_count')
            ->orderBy('name')
            ->take(8)
            ->get();

        return view('home', compact('featured', 'trades', 'auctions', 'categories'));
    }
}
