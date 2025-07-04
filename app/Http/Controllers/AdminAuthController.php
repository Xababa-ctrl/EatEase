<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {   
        // Affiche le formulaire de connexion pour les administrateurs.
        return view('admin.login');
    }

    public function login(Request $request)
    {   
        // Valide les données du formulaire de connexion.
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        // Récupère les identifiants du formulaire.
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            
            $request->session()->regenerate(); // Important pour la sécurité (évite fixation de session)

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();        // Invalidation de la session
        $request->session()->regenerateToken();   // Regénération du token CSRF
        
        return redirect()->route('admin.login');
    }
}
