<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $withRelations = ['user', 'category', 'mainPhoto', 'auction'];

        $featured = Listing::active()
            ->with($withRelations)
            ->latest()
            ->take(3)
            ->get();

        // Subastas abiertas, las que cierran primero van primero.
        $auctions = Listing::active()
            ->where('type', 'auction')
            ->whereHas('auction', fn ($q) => $q->where('status', 'open')->where('ends_at', '>', now()))
            ->with($withRelations)
            ->get()
            ->sortBy(fn (Listing $listing) => $listing->auction->ends_at)
            ->take(4);

        $categories = Category::withCount(['listings as active_count' => fn ($q) => $q->active()])
            ->orderByDesc('active_count')
            ->orderBy('name')
            ->take(9)
            ->get();

        $sellers = User::where('is_blocked', false)
            ->withCount(['listings as active_count' => fn ($q) => $q->active()])
            ->having('active_count', '>', 0)
            ->orderByDesc('active_count')
            ->take(5)
            ->get();

        $stats = [
            'listings' => Listing::active()->count(),
            'auctions' => $auctions->count(),
            'members' => User::where('is_blocked', false)->count(),
        ];

        return view('home', compact('featured', 'auctions', 'categories', 'sellers', 'stats'));
    }
}
