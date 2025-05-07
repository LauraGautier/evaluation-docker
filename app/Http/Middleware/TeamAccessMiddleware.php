<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeamAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si la requête concerne une route d'équipe
        if ($request->is('teams*')) {
            // Vérifie si l'utilisateur est connecté et s'il est manager
            if (!$request->user() || $request->user()->role !== 'manager') {
                return redirect()->route('access.denied')->with('error', 'Accès non autorisé aux fonctionnalités d\'équipe');
            }
        }

        return $next($request);
    }
}
