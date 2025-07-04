<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Mon modèle pour gérer les réservations.
// Chaque enregistrement dans la table 'reservations' correspondra à une demande de réservation.
class Reservation extends Model
{
    use HasFactory; // J'utilise le trait HasFactory, pratique pour générer des données de test.

    /**
     * Les attributs qui peuvent être assignés en masse.
     * C'est une mesure de sécurité importante dans Laravel.
     * Je liste ici tous les champs que je veux pouvoir remplir directement
     * lors de la création ou de la mise à jour d'une réservation.
     */
    protected $fillable = [
        'user_id',          // L'ID de l'utilisateur (client) qui fait la réservation (peut être null si non connecté).
        'restaurant_id',    // L'ID du restaurant pour lequel la réservation est faite.
        'customer_name',    // Nom du client (si non connecté ou si différent de l'utilisateur connecté).
        'customer_email',   // Email du client (si non connecté).
        'customer_phone',   // Téléphone du client (si non connecté).
        'reservation_time', // La date et l'heure de la réservation.
        'party_size',       // Le nombre de personnes pour la réservation.
        'notes',            // Des notes ou demandes spéciales pour la réservation.
        'status',           // Le statut de la réservation (ex: 'pending', 'confirmed', 'rejected', 'cancelled').
    ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     * 'reservation_time' sera automatiquement converti en objet Carbon (pour manipuler les dates/heures facilement).
     * 'party_size' sera automatiquement converti en entier (integer).
     */
    protected $casts = [
        'reservation_time' => 'datetime',
        'party_size' => 'integer',
    ];

    /**
     * Définit la relation "appartient à" (BelongsTo) avec le modèle Restaurant.
     * Chaque réservation est associée à un seul restaurant.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Définit la relation "appartient à" (BelongsTo) avec le modèle User.
     * Une réservation peut être associée à un utilisateur enregistré (client).
     * Cette relation peut être nullable si la réservation est faite par un visiteur non connecté.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}