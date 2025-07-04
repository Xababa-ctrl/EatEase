<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modèle représentant un administrateur de la plateforme.
 * 
 * Ce modèle est utilisé pour l'authentification et la gestion des comptes
 * administrateurs distincts des autres types d'utilisateurs (clients, restaurateurs).
 * Il étend la classe Authenticatable pour bénéficier du système d'auth Laravel.
 */
class Admin extends Authenticatable
{
    use Notifiable; // Permet aux admins de recevoir des notifications si besoin.

    /**
     * Attributs modifiables en masse (mass assignable).
     * 
     * On autorise ici uniquement les champs nécessaires à la création
     * ou la mise à jour d’un administrateur via un formulaire ou un seeder.
     */
    protected $fillable = [
        'name',         // Nom de l’administrateur
        'email',        // Adresse email (utilisée pour l’identification)
        'password',     // Mot de passe (crypté automatiquement via mutateur ou formulaire)
    ];

    /**
     * Attributs cachés lors de la sérialisation du modèle.
     * 
     * Ces champs ne seront pas visibles lorsqu’on renvoie l’objet sous forme de JSON
     * (ex : dans les API ou les logs) afin de garantir la sécurité.
     */
    protected $hidden = [
        'password',         // Empêche l’exposition du mot de passe même crypté
        'remember_token',   // Jeton utilisé par Laravel pour la reconnexion automatique
    ];
}
