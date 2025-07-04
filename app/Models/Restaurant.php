<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restaurant extends Model
{
    use HasFactory; // J'utilise le trait HasFactory pour faciliter la génération de données factices pendant les tests.

    /**
     * Les attributs pouvant être remplis en masse.
     * Cela permet de protéger contre les attaques de type mass assignment.
     */
    protected $fillable = [
        'name',         // Nom du restaurant
        'description',  // Description du restaurant
        'address',      // Adresse
        'phone_number', // Numéro de téléphone
        'user_id',      // ID du restaurateur propriétaire
    ];

    /**
     * Relation : Un restaurant peut avoir plusieurs réservations.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relation : Un restaurant peut avoir plusieurs horaires d'ouverture.
     * Les horaires sont triés par jour puis par heure.
     */
    public function openingHours(): HasMany
    {
        return $this->hasMany(OpeningHour::class)->orderBy('day_of_week')->orderBy('open_time');
    }

    /**
     * Relation : Chaque restaurant appartient à un utilisateur (le restaurateur).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation : Un restaurant peut avoir plusieurs avis clients.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relation : Un restaurant peut avoir plusieurs images dans sa galerie.
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Méthode utilitaire : Calcule la note moyenne des avis du restaurant.
     * 
     * Retourne null s'il n'y a pas encore d'avis.
     */
    public function averageRating(): ?float
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * Méthode d'événement Eloquent : suppression en cascade.
     * 
     * Avant de supprimer un restaurant, je supprime d'abord :
     * - Ses galeries
     * - Ses avis
     * - Ses réservations
     * 
     * Cela évite de laisser des données orphelines en base.
     */
    protected static function booted()
    {
        static::deleting(function ($restaurant) {
            // Suppression des galeries associées
            $restaurant->galleries()->delete();

            // Suppression des avis associés
            $restaurant->reviews()->delete();

            // Suppression des réservations associées
            $restaurant->reservations()->delete();
        });
    }
}
