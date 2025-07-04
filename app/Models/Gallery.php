<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle représentant une image de la galerie associée à un restaurant.
 * 
 * Chaque enregistrement correspond à une photo stockée (ex : intérieur du restaurant, plat, ambiance).
 * Ce modèle est lié à la table `galleries` et possède une relation directe avec le modèle `Restaurant`.
 */
class Gallery extends Model
{
    use HasFactory; // Active la factory associée pour générer facilement des images en base lors des tests/seeds.

    /**
     * Champs pouvant être remplis automatiquement (mass assignables).
     * 
     * - restaurant_id : identifiant du restaurant lié à cette photo.
     * - photo_path    : chemin vers la photo enregistrée (dans le système de fichiers ou storage Laravel).
     */
    protected $fillable = [
        'restaurant_id',
        'photo_path',
    ];

    /**
     * Relation : une photo appartient à un restaurant.
     * 
     * Cela permet d'accéder au restaurant parent depuis une image via $gallery->restaurant.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
