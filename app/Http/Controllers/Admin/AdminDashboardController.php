<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administrateur.
     * 
     * Cette méthode permet de récupérer plusieurs statistiques globales
     * afin de donner une vue d'ensemble rapide de l'activité du site.
     * 
     * Les données récupérées sont ensuite transmises à la vue 'admin.dashboard'
     * où elles seront affichées sous forme de chiffres clés (KPIs).
     */
    public function index()
    {
        /**
         * Statistiques sur les utilisateurs :
         * - Nombre total d'utilisateurs
         * - Nombre de clients
         * - Nombre de restaurateurs
         * - Nombre d'administrateurs
         */
        $totalUsers = User::count();
        $totalClients = User::where('role', 'client')->count();
        $totalRestaurateurs = User::where('role', 'restaurateur')->count();
        $totalAdmins = Admin::count();

        /**
         * Statistiques sur les restaurants :
         * - Nombre total de restaurants enregistrés
         */
        $totalRestaurants = Restaurant::count();

        /**
         * Statistiques sur les réservations :
         * - Nombre total de réservations sur la plateforme
         * - Nombre de réservations créées aujourd'hui (statistique journalière)
         */
        $totalReservations = Reservation::count();
        $reservationsToday = Reservation::whereDate('created_at', now()->toDateString())->count();

        /**
         * Je retourne la vue du dashboard admin avec toutes les statistiques sous forme de variables compactées.
         * Ces données seront ensuite affichées dans la vue pour que l'administrateur puisse suivre l'activité en temps réel.
         */
        return view('admin.dashboard', compact(
            'totalUsers', 'totalClients', 'totalRestaurateurs', 'totalAdmins',
            'totalRestaurants', 'totalReservations', 'reservationsToday'
        ));
    }
}
