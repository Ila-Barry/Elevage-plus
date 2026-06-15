<?php
// database/migrations/2025_01_01_000005_add_reminder_columns_to_taches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('taches', function (Blueprint $table) {
            // Supprimer les anciennes colonnes si elles existent
            if (Schema::hasColumn('taches', 'rappel')) {
                $table->dropColumn('rappel');
            }
            if (Schema::hasColumn('taches', 'dernier_rappel_envoye')) {
                $table->dropColumn('dernier_rappel_envoye');
            }
            if (Schema::hasColumn('taches', 'rappel_compteur')) {
                $table->dropColumn('rappel_compteur');
            }
            
            // Ajouter les nouvelles colonnes
            $table->enum('last_reminder_type', ['48h', '24h', '1h', 'retard'])->nullable()->after('priorite');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('last_reminder_type');
            $table->integer('retard_reminder_count')->default(0)->after('last_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('taches', function (Blueprint $table) {
            $table->dropColumn([
                'last_reminder_type',
                'last_reminder_sent_at',
                'retard_reminder_count',
            ]);
            
            // Recréer les anciennes colonnes si nécessaire
            $table->enum('rappel', ['48h', '24h', '1h', '30min', '0'])->nullable();
            $table->timestamp('dernier_rappel_envoye')->nullable();
            $table->integer('rappel_compteur')->default(0);
        });
    }
};