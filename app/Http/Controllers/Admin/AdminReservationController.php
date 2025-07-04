<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Restaurant;

class AdminReservationController extends Controller
{
    /**
     * Affiche la liste de toutes les réservations du site.
     * 
     * J'utilise ici la méthode Eloquent with() pour récupérer aussi les informations du restaurant
     * associé à chaque réservation, afin de les afficher plus facilement dans la vue.
     * Les réservations sont triées de la plus récente à la plus ancienne grâce à latest().
     */
    public function index()
    {
        $reservations = Reservation::with('restaurant')->latest()->get();

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Affiche le formulaire de modification d'une réservation.
     * 
     * Cette méthode permet à l'administrateur de modifier les détails d'une réservation existante :
     * - Informations du client
     * - Horaire
     * - Taille du groupe
     * - Statut de la réservation (en attente, confirmée, rejetée)
     * - Restaurant associé
     * 
     * Je récupère aussi la liste complète des restaurants pour permettre à l'admin de changer
     * le restaurant concerné si nécessaire.
     */
    public function edit($id)
    {
        $reservation = Reservation::findOrFail($id); // Je vérifie que la réservation existe
        $restaurants = Restaurant::all(); // Je récupère tous les restaurants pour le menu déroulant dans le formulaire

        return view('admin.reservations.edit', compact('reservation', 'restaurants'));
    }

    /**
     * Traite la mise à jour d'une réservation.
     * 
     * Je commence par valider les données envoyées depuis le formulaire pour garantir leur cohérence.
     * 
     * Les champs validés sont :
     * - ID du restaurant (qui doit exister en base)
     * - Nom, email, téléphone du client
     * - Date et heure de réservation
     * - Taille du groupe
     * - Statut de la réservation (avec liste de valeurs autorisées)
     * 
     * Si tout est correct, je mets à jour la réservation et je redirige l'admin avec un message de succès.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'reservation_time' => 'required|date',
            'party_size' => 'required|integer|min:1',
            'status' => 'required|in:pending,confirmed,rejected',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update($validated);

        return redirect()->route('admin.reservations.index')->with('success', 'Réservation mise à jour');
    }

    /**
     * Supprime une réservation de la base de données.
     * 
     * Avant la suppression, je vérifie que la réservation existe avec findOrFail().
     * Une fois supprimée, je redirige l'admin vers la liste des réservations avec un message de confirmation.
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return redirect()->route('admin.reservations.index')->with('success', 'Réservation supprimée');
    }
}
