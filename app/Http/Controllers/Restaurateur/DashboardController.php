<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $restaurant = $user->restaurant; // Récupère le restaurant via la relation

        // Vérifie si le restaurant est associé
        if (!$restaurant) {
            // Gérer ce cas : afficher un message, rediriger vers une page de configuration...
            // Pour l'instant, on passe null à la vue qui devra le gérer
        }

        // Récupérer des données pour le dashboard (ex: réservations en attente)
        $pendingReservationsCount = 0;
        if ($restaurant) {
            $pendingReservationsCount = $restaurant->reservations()
                                                ->where('status', 'pending')
                                                ->count();
        }


        return view('restaurateur.dashboard', compact('restaurant', 'pendingReservationsCount'));
    }
}

