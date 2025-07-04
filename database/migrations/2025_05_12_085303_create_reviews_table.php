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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'utilisateur qui a laissé l'avis
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade'); // Le restaurant concerné
            $table->unsignedTinyInteger('rating'); // Note (ex: de 1 à 5)
            $table->text('comment')->nullable(); // Commentaire textuel
            $table->timestamps(); // created_at et updated_at

            // Optionnel: Un utilisateur ne peut laisser qu'un avis par restaurant
            // $table->unique(['user_id', 'restaurant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};