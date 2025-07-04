<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_user_id_to_restaurants_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Ajoute la clé étrangère user_id
            $table->foreignId('user_id')
                  ->nullable() // Ou non nullable si un restaurant DOIT avoir un propriétaire
                  ->after('id') // Ou où vous préférez
                  ->constrained('users') // Lie à la table 'users'
                  ->onDelete('cascade'); // Optionnel: supprime les restaurants si l'utilisateur est supprimé
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Important de supprimer la contrainte avant la colonne
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};

