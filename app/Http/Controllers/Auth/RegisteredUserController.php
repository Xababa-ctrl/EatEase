<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    /**
     * Affiche le formulaire d'inscription.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur.
     */
    public function store(Request $request): RedirectResponse
    {
        // Je commence par valider les données du formulaire
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['customer', 'restaurateur'])], // Je contrôle bien les rôles autorisés.
        ]);

        // Je crée l'utilisateur avec les données validées.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Je connecte automatiquement l'utilisateur après l'inscription.
        Auth::login($user);

        // Je redirige l'utilisateur vers un tableau de bord spécifique selon son rôle.
        if ($user->role === 'restaurateur') {
            return redirect()->route('restaurateur.dashboard')->with('success', 'Bienvenue dans votre espace restaurateur !');
        }

        // Par défaut, les autres rôles (ex : client) seront redirigés vers la page d’accueil.
        return redirect('/')->with('success', 'Inscription réussie ! Vous êtes connecté.');
    }
}
