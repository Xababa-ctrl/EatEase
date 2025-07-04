<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            // Jour de la semaine: 1 pour Lundi, 7 pour Dimanche (norme ISO 8601)
            $table->unsignedTinyInteger('day_of_week');
            $table->time('open_time'); // Format HH:MM:SS ou HH:MM
            $table->time('close_time'); // Format HH:MM:SS ou HH:MM
            // $table->timestamps(); // Optionnel, si vous voulez savoir quand un horaire a été ajouté/modifié

            // Index pour améliorer les recherches par restaurant et jour
            $table->index(['restaurant_id', 'day_of_week']);
            // Contrainte pour s'assurer qu'un restaurant n'a pas le même jour défini plusieurs fois
            // (Attention: un restaurant peut avoir plusieurs créneaux par jour, ex: midi et soir.
            // Si c'est le cas, cette contrainte unique n'est pas appropriée, ou il faut ajouter open/close time dedans)
            // $table->unique(['restaurant_id', 'day_of_week', 'open_time']); // Décommentez et adaptez si besoin de plusieurs créneaux/jour
            
            // Ajouter les colonnes created_at et updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_hours');
    }
};
