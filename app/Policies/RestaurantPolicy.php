<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// Ma "Police" pour les Restaurants. C'est ici que je définis toutes les règles
// pour savoir qui a le droit de faire quoi avec les objets Restaurant.
// Par exemple : qui peut créer un restaurant ? Qui peut le modifier ? etc.
class RestaurantPolicy
{
    // J'utilise le trait HandlesAuthorization. Il n'est pas toujours strictement nécessaire
    // si on écrit des logiques simples, mais il peut être utile pour des cas plus complexes
    // ou pour utiliser des méthodes comme allow() ou deny() directement.
    use HandlesAuthorization; 

    /**
     * Détermine si l'utilisateur donné peut créer un nouveau restaurant.
     * Seuls les utilisateurs ayant le rôle de 'restaurateur' peuvent le faire.
     */
    public function create(User $user): bool
    {
        return $user->isRestaurateur();
    }

    /**
     * Détermine si l'utilisateur donné peut modifier le restaurant spécifié.
     * L'utilisateur doit être le propriétaire du restaurant ET avoir le rôle de 'restaurateur'.
     */
    public function update(User $user, Restaurant $restaurant): bool
    {
        // Je vérifie que l'ID de l'utilisateur correspond à l'user_id du restaurant
        // ET que l'utilisateur est bien un restaurateur.
        return $user->id === $restaurant->user_id && $user->isRestaurateur();
    }

    /**
     * Détermine si l'utilisateur donné peut supprimer le restaurant spécifié.
     * L'utilisateur doit être le propriétaire du restaurant ET avoir le rôle de 'restaurateur'.
     */
    public function delete(User $user, Restaurant $restaurant): bool
    {
        // Mêmes conditions que pour la mise à jour.
        return $user->id === $restaurant->user_id && $user->isRestaurateur();
    }

    /**
     * Détermine si l'utilisateur donné peut voir la liste des restaurants.
     * Ici, tout le monde (même les visiteurs non connectés, d'où le ?User) peut voir la liste.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur donné peut voir un restaurant spécifique.
     * Ici, tout le monde (même les visiteurs non connectés) peut voir les détails d'un restaurant.
     */
    public function view(?User $user, Restaurant $restaurant): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur donné peut gérer les réservations pour le restaurant spécifié.
     * L'utilisateur doit être le propriétaire du restaurant ET avoir le rôle de 'restaurateur'.
     * Cette méthode n'est pas utilisée par défaut par Laravel pour les actions CRUD,
     * mais je peux l'appeler manuellement dans mes contrôleurs si besoin (par exemple, $this->authorize('manageReservations', $restaurant)).
     */
    public function manageReservations(User $user, Restaurant $restaurant): bool
    {
        return $user->id === $restaurant->user_id && $user->isRestaurateur();
    }

    /**
     * Détermine si l'utilisateur donné peut gérer les avis pour le restaurant spécifié.
     * L'utilisateur doit être le propriétaire du restaurant ET avoir le rôle de 'restaurateur'.
     * Idem, c'est une méthode personnalisée que je peux utiliser au besoin.
     */
    public function manageReviews(User $user, Restaurant $restaurant): bool
    {
        return $user->id === $restaurant->user_id && $user->isRestaurateur();
    }
}