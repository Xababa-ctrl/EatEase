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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Client connecté (peut être null si non connecté)
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade'); // Restaurant réservé (requis)
            // Infos client si non connecté
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            // Détails réservation
            $table->dateTime('reservation_time'); // Date et heure combinées
            $table->integer('party_size'); // Nombre de personnes
            $table->text('notes')->nullable(); // Notes spéciales du client
            $table->string('status')->default('pending'); // Statut: pending, confirmed, rejected, cancelled_by_user, cancelled_by_restaurant, completed
            $table->timestamps(); // created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
