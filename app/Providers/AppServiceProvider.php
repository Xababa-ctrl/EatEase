<?php

namespace App\Providers;

// J'importe la classe ServiceProvider de base de Laravel.
use Illuminate\Support\ServiceProvider;

// Mon AppServiceProvider. C'est un fournisseur de services un peu "fourre-tout"
// où je peux enregistrer des liaisons personnalisées dans le conteneur de services
// ou exécuter du code au démarrage de mon application.
class AppServiceProvider extends ServiceProvider
{
    /**
     * Enregistre tous les services de l'application.
     *
     * Cette méthode est appelée AVANT la méthode boot().
     * C'est ici que je peux lier des choses dans le conteneur de services de Laravel.
     * Par exemple, si j'ai une interface et une implémentation, je les lie ici.
     * Pour l'instant, je n'ai rien de spécifique à enregistrer ici.
     */
    public function register(): void
    {
        // Exemple : $this->app->bind(MaInterface::class, MaClasseConcrete::class);
    }

    /**
     * Démarre (bootstrap) tous les services de l'application.
     *
     * Cette méthode est appelée APRÈS que tous les autres fournisseurs de services ont été enregistrés (y compris ceux dans register()).
     * C'est un bon endroit pour des choses comme enregistrer des View Composers, des Gates, des observateurs de modèles, etc.
     * Pour l'instant, je n'ai rien de spécifique à démarrer ici.
     */
    public function boot(): void
    {
        // Exemple : View::composer('profile', ProfileComposer::class);
    }
}
