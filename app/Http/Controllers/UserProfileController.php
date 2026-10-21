<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Página pública de un vendedor (distinta de "Mi perfil", que es el formulario de edición).
class UserProfileController extends Controller
{
    public function show(Request $request, User $user): View
    {
        abort_if($user->is_blocked, 404);

        $filter = $request->query('filtro');

        $query = $user->listings()
            ->active()
            ->with(['user', 'category', 'mainPhoto', 'auction'])
            ->latest();

        if ($filter === 'venta') {
            $query->where('type', 'sale');
        } elseif ($filter === 'subasta') {
            $query->where('type', 'auction');
        } elseif ($filter === 'trueque') {
            $query->where('accepts_trade', true);
        } else {
            $filter = null;
        }

        return view('users.show', [
            'user' => $user,
            'filter' => $filter,
            'listings' => $query->paginate(9)->withQueryString(),
            'activeCount' => $user->listings()->active()->count(),
            'wonCount' => $user->wonAuctions()->count(),
        ]);
    }
}
