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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id(); // Crée une colonne 'id' auto-incrémentée (BIGINT UNSIGNED)
            $table->string('name');
            $table->text('description')->nullable(); // TEXT, peut être NULL
            $table->string('address');
            $table->string('phone_number', 20)->nullable(); // VARCHAR(20), peut être NULL
            // Ajoutez d'autres colonnes si nécessaire (ex: $table->string('city');)
            $table->timestamps(); // Crée les colonnes 'created_at' et 'updated_at' (TIMESTAMP nullable)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
