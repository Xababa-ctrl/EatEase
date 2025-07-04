<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Mon modèle pour gérer les avis laissés par les utilisateurs sur les restaurants.
// Chaque instance de ce modèle correspondra à un avis dans ma base de données.
class Review extends Model
{
    use HasFactory; // J'utilise le trait HasFactory, utile pour créer des données de test.

    /**
     * Les attributs qui peuvent être assignés en masse.
     * C'est une mesure de sécurité pour contrôler quels champs peuvent être remplis
     * directement via une requête (par exemple, lors de la création d'un avis).
     */
    protected $fillable = [
        'user_id',       // L'ID de l'utilisateur (client) qui a laissé l'avis.
        'restaurant_id', // L'ID du restaurant pour lequel l'avis a été laissé.
        'rating',        // La note donnée par l'utilisateur (par exemple, de 1 à 5).
        'comment',       // Le commentaire textuel de l'utilisateur.
    ];

    /**
     * Définit la relation "appartient à" (BelongsTo) avec le modèle User.
     * Chaque avis est laissé par un seul utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Définit la relation "appartient à" (BelongsTo) avec le modèle Restaurant.
     * Chaque avis concerne un seul restaurant.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}

// On pourrait aussi ajouter des $casts ici si on voulait s'assurer que 'rating' est toujours un entier,
// par exemple : protected $casts = ['rating' => 'integer'];
// Mais la validation dans le contrôleur s'en charge déjà.
