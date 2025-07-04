<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable; // Permet d'envoyer des notifications à l'utilisateur.

    /**
     * Les attributs pouvant être remplis en masse.
     * Cela protège contre les attaques de type "mass assignment".
     */
    protected $fillable = [
        'name',     // Nom de l'utilisateur.
        'email',    // Email unique de l'utilisateur.
        'password', // Mot de passe (qui sera automatiquement haché grâce au cast).
        'role',     // Rôle de l'utilisateur (client, restaurateur, admin...).
    ];

    /**
     * Les attributs cachés lors de la sérialisation (par exemple en JSON).
     * Cela évite d'exposer des données sensibles comme le mot de passe.
     */
    protected $hidden = [
        'password',         // Toujours masquer le mot de passe.
        'remember_token',   // Masquer le token "se souvenir de moi".
    ];

    /**
     * Les casts : conversion automatique de certains champs.
     * Exemple : la date de vérification d'email sera automatiquement gérée comme un objet DateTime.
     * Laravel hachera automatiquement le mot de passe quand on le définit.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relation : Un restaurateur peut avoir plusieurs restaurants.
     */
    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    /**
     * Relation : Un client peut avoir plusieurs avis.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Méthode utilitaire : Vérifie si l'utilisateur est un restaurateur.
     */
    public function isRestaurateur(): bool
    {
        return $this->role === 'restaurateur';
    }

    /**
     * Méthode utilitaire : Vérifie si l'utilisateur est un client.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Suppression en cascade des restaurants lorsqu'un restaurateur est supprimé.
     * 
     * Avant de supprimer un utilisateur, on vérifie si c'est un restaurateur.
     * Si c'est le cas, on supprime automatiquement tous ses restaurants.
     * 
     * Comme chaque restaurant possède lui-même des galeries, avis et réservations,
     * ces éléments seront aussi supprimés grâce à la cascade déjà définie dans Restaurant.php.
     */
    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->role === 'restaurateur') {
                foreach ($user->restaurants as $restaurant) {
                    $restaurant->delete(); // Cela déclenche à son tour le deleting() du modèle Restaurant.
                }
            }
        });
    }
}
