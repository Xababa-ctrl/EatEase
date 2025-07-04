<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajoute la colonne 'role' après 'email' (ou où vous voulez)
            // 'customer' par défaut, 'restaurateur', 'admin' pourraient être d'autres options
            $table->string('role')->default('customer')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
