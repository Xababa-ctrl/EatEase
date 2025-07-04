<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Enregistre un nouvel avis pour un restaurant.
     * Seuls les clients peuvent laisser des avis.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        // D'abord, je vérifie si l'utilisateur connecté est bien un client.
        // La méthode isCustomer() doit être définie dans le modèle User.
        if (!Auth::user()->isCustomer()) {
            return back()->with('error', 'Seuls les clients peuvent laisser des avis.');
        }

        // Ensuite, je valide les données envoyées par le formulaire.
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // TODO: On pourrait ajouter une vérification ici pour s'assurer
        // qu'un utilisateur ne laisse pas plusieurs avis pour le même restaurant.

        Review::create([
            'user_id' => Auth::id(), // J'associe l'avis à l'utilisateur connecté.
            'restaurant_id' => $restaurant->id, // J'associe l'avis au restaurant concerné.
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Merci pour votre avis !');
    }
}
