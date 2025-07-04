<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// ^ Ça, c'est pour pouvoir vérifier si un utilisateur a le droit de faire certaines actions (autorisations).
use Illuminate\Foundation\Validation\ValidatesRequests;
// ^ Et ça, c'est pour pouvoir valider facilement les données qui viennent des formulaires.
use Illuminate\Routing\Controller as BaseController;
// ^ Ça, c'est la classe "Controller" de base de Laravel, dont mon propre Controller va hériter.

// Le mot "abstract" ici signifie que je ne vais jamais créer directement un objet de CETTE classe Controller.
// Par contre, tous mes autres contrôleurs (comme RestaurantController, ContactController, etc.)
// vont "hériter" de celui-ci. C'est comme une classe "modèle" pour mes autres contrôleurs.
abstract class Controller extends BaseController
{
    // En utilisant ces "traits" (un peu comme des copier-coller de fonctionnalités),
    // je donne à TOUS mes contrôleurs qui héritent de celui-ci les capacités
    // de gérer les autorisations (avec AuthorizesRequests) et la validation des données (avec ValidatesRequests).
    // C'est super pratique, ça évite de répéter du code partout !
    use AuthorizesRequests, ValidatesRequests;
}
