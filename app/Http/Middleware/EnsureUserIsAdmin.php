<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        
        $user = $request->user();

         abort_unless($user !== null && $user->isAdmin(), 403); //corta con erroe 403 si el usuario no es admin 

        return $next($request);
    }
}
