<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur connecté a bien le rôle attendu.
     * Usage dans les routes : middleware('role:admin') ou middleware('role:proprietaire')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role !== $role) {
            abort(403, 'Accès non autorisé.');
        }

        // Pour les propriétaires : vérifier que le compte est actif
        if ($role === 'proprietaire' && !$user->isActif()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte est en attente de validation par l\'administrateur.');
        }

        return $next($request);
    }
}
