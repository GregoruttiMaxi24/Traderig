<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    // Portada del panel de administración. La moderación completa llega en la Fase 7.
    public function index(): View
    {
        return view('admin.index', [
            'usersCount' => User::count(),
            'listingsCount' => Listing::count(),
            'latestUsers' => User::latest()->take(10)->get(),
        ]);
    }
}
