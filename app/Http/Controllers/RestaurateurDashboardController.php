<?php

namespace App\Http\Controllers;

// Mon contrôleur pour la gestion du tableau de bord des restaurateurs.

// Importation des classes nécessaires
use Illuminate\Support\Facades\Auth; // Pour récupérer l'utilisateur connecté.
use App\Models\Restaurant;           // Modèle des restaurants.
use App\Models\Reservation;          // Modèle des réservations.
use Illuminate\View\View;            // Pour les méthodes qui retournent une vue HTML.
use Illuminate\Http\RedirectResponse; // Pour les méthodes qui redirigent.

class RestaurateurDashboardController extends Controller
{
    /**
     * Affiche la page principale du tableau de bord du restaurateur.
     * 
     * Liste tous les restaurants appartenant à l'utilisateur connecté.
     */
    public function index(): View
    {
        $user = Auth::user(); // Je récupère l'utilisateur actuellement connecté.

        // Je récupère ses restaurants, triés par nom.
        $restaurants = $user->restaurants()->orderBy('name')->get();

        // Je retourne la vue du tableau de bord avec la liste des restaurants.
        return view('restaurateur.dashboard', compact('restaurants'));
    }

    /**
     * Affiche la liste paginée des restaurants du restaurateur.
     * 
     * Cette méthode sert pour la page "Voir mes restaurants".
     */
    public function listRestaurants(): View
    {
        $user = Auth::user(); // Toujours récupérer l'utilisateur connecté.

        $restaurants = $user->restaurants()
                            ->latest() // Trie par date de création (plus récents en premier)
                            ->paginate(10); // Je pagine les résultats (10 par page).

        // Je réutilise la vue publique des restaurants mais avec un contenu filtré.
        return view('restaurants.index', compact('restaurants'));
    }

    /**
     * Affiche la liste paginée des réservations pour tous les restaurants du restaurateur connecté.
     */
    public function reservations(): View
    {
        $user = Auth::user();
        
        // Je peux simplifier cette logique en utilisant `whereHas`.
        // Cela me permet de récupérer directement les réservations
        // qui ont une relation 'restaurant' appartenant à l'utilisateur connecté.
        // Cette approche est plus "Eloquent" et souvent plus lisible.
        $reservations = Reservation::whereHas('restaurant', function ($query) use ($user) {
                                       $query->where('user_id', $user->id);
                                   })
                                   ->with(['restaurant', 'user'])
                                   ->orderBy('reservation_time', 'desc') // Trie par date de réservation (plus récentes d'abord)
                                   ->paginate(15); // Pagine les résultats (15 par page).

        return view('restaurateur.reservations', compact('reservations'));
    }

    /**
     * Confirme une réservation spécifique.
     */
    public function confirmReservation(Reservation $reservation): RedirectResponse
    {
        // Je précharge la relation 'restaurant' pour éviter un nouvel appel SQL plus bas.
        $reservation->loadMissing('restaurant');

        // D'abord, je m'assure que la réservation a bien un restaurant associé.
        if (!$reservation->restaurant) {
            return redirect()->route('restaurateur.reservations')->with('error', 'Restaurant non trouvé pour cette réservation.');
        }

        // Je vérifie que le restaurant de cette réservation appartient bien au restaurateur connecté.
        // J'utilise la Policy pour une meilleure pratique et une vérification plus complète.
        $this->authorize('manageReservations', $reservation->restaurant);

        // Je mets à jour le statut seulement si la réservation est encore "en attente".
        if ($reservation->status === 'pending') {
            $reservation->update(['status' => 'confirmed']);

            return redirect()->route('restaurateur.reservations')
                             ->with('success', 'Réservation confirmée avec succès.');
        }

        return redirect()->route('restaurateur.reservations')
                         ->with('warning', 'Cette réservation ne peut plus être confirmée.');
    }

    /**
     * Rejette une réservation spécifique.
     */
    public function rejectReservation(Reservation $reservation): RedirectResponse
    {
        // Je précharge aussi ici la relation pour optimiser les requêtes.
        $reservation->loadMissing('restaurant');

        // D'abord, je m'assure que la réservation a bien un restaurant associé.
        if (!$reservation->restaurant) {
            return redirect()->route('restaurateur.reservations')->with('error', 'Restaurant non trouvé pour cette réservation.');
        }

        // Même vérification que pour la confirmation.
        $this->authorize('manageReservations', $reservation->restaurant);

        if ($reservation->status === 'pending') {
            $reservation->update(['status' => 'rejected']);

            return redirect()->route('restaurateur.reservations')
                             ->with('success', 'Réservation rejetée.');
        }

        return redirect()->route('restaurateur.reservations')
                         ->with('warning', 'Cette réservation ne peut plus être rejetée.');
    }
}
