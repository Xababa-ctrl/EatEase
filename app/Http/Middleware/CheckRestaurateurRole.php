<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// Ce middleware va vérifier si l'utilisateur
// qui essaie d'accéder à une route est bien un "restaurateur".
class CheckRestaurateurRole
{
    /**
     * Ma méthode principale qui va intercepter la requête.
     * Si tout va bien, elle laisse passer la requête ($next($request)).
     * Sinon, elle redirige l'utilisateur.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Première chose : est-ce que l'utilisateur est au moins connecté ?
        if (!Auth::check()) {
            // Si non, je le renvoie direct à la page de connexion.
            return redirect()->route('login');
        }

        // Deuxième chose : est-ce que l'utilisateur connecté a bien le rôle de "restaurateur" ?
        // La méthode isRestaurateur() doit être définie dans mon modèle User.
        if (!Auth::user()->isRestaurateur()) {
            // Si non, je le redirige vers la page d'accueil avec un message d'erreur.
            return redirect('/')->with('error', 'Accès réservé aux restaurateurs.');
        }

        // Si on arrive jusqu'ici, c'est que l'utilisateur est connecté ET qu'il est restaurateur.
        // Donc, je laisse la requête continuer son chemin vers le contrôleur.
        return $next($request);
    }
}
