<?php

namespace App\Providers;

// J'importe les modèles et policies nécessaires.
use App\Models\Restaurant;
use App\Policies\RestaurantPolicy;
// J'importe la classe ServiceProvider de base pour l'authentification/autorisation de Laravel.
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


// Mon AuthServiceProvider. C'est le QG pour tout ce qui est authentification et autorisation.
// C'est ici que je dis à Laravel quelles "Policies" (règles d'accès) utiliser pour mes modèles.
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les correspondances entre mes modèles et leurs policies (règles d'autorisation).
     *
     * Laravel utilisera automatiquement la policy associée à un modèle
     * quand j'appelle des méthodes comme $this->authorize() dans mes contrôleurs
     * ou les directives @can() dans mes vues Blade.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // Ici, je dis : "Pour le modèle Restaurant, utilise la RestaurantPolicy."
        Restaurant::class => RestaurantPolicy::class,
    ];

    /**
     * Enregistre tous les services d'authentification / autorisation.
     *
     * Cette méthode est appelée par Laravel pour enregistrer mes policies et mes gates (si j'en avais).
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ...
    }
}
