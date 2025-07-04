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
         Schema::create('galleries', function (Blueprint $table) {
            $table->id(); // Colonne ID auto-incrémentée et clé primaire
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade'); // Clé étrangère vers la table restaurants, suppression en cascade
            $table->string('photo_path'); // Chemin vers le fichier image
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
