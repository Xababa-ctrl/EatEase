<?php

namespace App\Http;

// J'importe la classe Kernel de base de Laravel, dont mon propre Kernel va hériter.
use Illuminate\Foundation\Http\Kernel as HttpKernel;

// Ma classe Kernel hérite du HttpKernel de Laravel.
// C'est le cœur du traitement des requêtes HTTP pour mon application.
class Kernel extends HttpKernel
{
    /**
     * Les middlewares de route de mon application.
     *
     * Ces middlewares peuvent être assignés à des groupes ou utilisés individuellement sur des routes.
     * Je donne un 'alias' (un petit nom) à chaque classe de middleware pour pouvoir l'utiliser
     * facilement dans mes fichiers de routes (par exemple, routes/web.php).
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ... autres middlewares
        // 'auth' : C'est le middleware de base de Laravel pour vérifier si un utilisateur est connecté.
        'auth' => \App\Http\Middleware\Authenticate::class,
        // 'guest' : C'est le middleware de base de Laravel pour les visiteurs non connectés (par exemple, pour les rediriger s'ils essaient d'accéder à une page de connexion alors qu'ils le sont déjà).
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        // 'restaurateur' : C'est MON middleware personnalisé ! Il vérifie si l'utilisateur est connecté ET s'il a le rôle de "restaurateur".
        'restaurateur' => \App\Http\Middleware\CheckRestaurateurRole::class,
    ];
}
