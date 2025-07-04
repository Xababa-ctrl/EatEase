<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Mon modèle pour gérer les horaires d'ouverture d'un restaurant.
// Chaque enregistrement dans la table 'opening_hours' correspondra à une plage horaire pour un jour donné.
class OpeningHour extends Model
{
    use HasFactory; // J'utilise le trait HasFactory, utile si je veux créer des données de test facilement.

    // La ligne suivante, si elle était décommentée, indiquerait à Laravel
    // de NE PAS gérer automatiquement les colonnes 'created_at' et 'updated_at'.
    // C'est parfois utile pour des tables qui n'ont pas besoin de ces timestamps.
    // Pour l'instant, je les laisse gérés par Laravel (donc cette ligne reste commentée).
    // public $timestamps = false;

    /**
     * Les attributs qui peuvent être assignés en masse.
     * C'est une mesure de sécurité pour éviter que n'importe quel champ
     * puisse être modifié via une requête HTTP.
     * Ici, je permets de remplir 'restaurant_id', 'day_of_week', 'open_time', et 'close_time'.
     */
    protected $fillable = [
        'restaurant_id', // L'ID du restaurant auquel cet horaire appartient.
        'day_of_week',   // Le jour de la semaine (par exemple, 1 pour Lundi, 7 pour Dimanche).
        'open_time',     // L'heure d'ouverture (format HH:MM:SS).
        'close_time',    // L'heure de fermeture (format HH:MM:SS).
    ];

    /**
     * Définit la relation "appartient à" (BelongsTo) avec le modèle Restaurant.
     * Un horaire d'ouverture appartient toujours à un seul restaurant.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
