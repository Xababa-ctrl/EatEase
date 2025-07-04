<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function create(): View
    {   
    
        return view('auth.login'); // On va créer cette vue
    }

    /**
     * Gère une requête d'authentification.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Tente d'authentifier l'utilisateur
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember'); // Champ 'remember' dans le formulaire

        if (Auth::attempt($credentials, $remember)) {
            // Authentification réussie
            $request->session()->regenerate(); // Important pour la sécurité (évite fixation de session)

            $user = Auth::user();
            // Vérifier le rôle de l'utilisateur après l'authentification
            if ($user->role === 'restaurateur') {
                return redirect()->route('restaurateur.dashboard')->with('success', 'Connexion réussie !');
            }

            // Redirection par défaut pour les autres rôles (ex: client)
            return redirect()->intended('/')->with('success', 'Connexion réussie !');

        }

        // Authentification échouée
        throw ValidationException::withMessages([
            'email' => __('auth.failed'), // Utilise les traductions Laravel
        ]);
    }

    /**
     * Détruit une session authentifiée (déconnexion).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout(); // Déconnecte l'utilisateur

        $request->session()->invalidate(); // Invalide la session

        $request->session()->regenerateToken(); // Régénère le token CSRF

        return redirect('/')->with('success', 'Vous avez été déconnecté.');
    }
}
