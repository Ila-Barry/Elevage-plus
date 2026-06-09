<?php
// database/migrations/2025_01_01_000004_create_taches_table.php

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
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->nullable()->constrained('animaux')->onDelete('cascade');
            $table->foreignId('elevage_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('titre', 200);
            $table->enum('type', [
                'vaccination',
                'pesee',
                'vermifuge',
                'soin',
                'nettoyage',
                'alimentation',
                'reproduction',
                'visite_veterinaire',
                'autre'
            ])->default('autre');
            $table->text('description')->nullable();
            $table->datetime('date_planifiee');
            $table->datetime('date_realisee')->nullable();
            $table->boolean('terminee')->default(false);
            $table->enum('priorite', ['basse', 'moyenne', 'haute', 'urgente'])->default('moyenne');
            $table->enum('rappel', ['48h', '24h', '1h', '30min', '0'])->nullable();
            $table->datetime('dernier_rappel_envoye')->nullable();
            $table->integer('rappel_compteur')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index pour les performances
            $table->index('animal_id');
            $table->index('elevage_id');
            $table->index('user_id');
            $table->index('date_planifiee');
            $table->index('terminee');
            $table->index('type');
            $table->index('priorite');
            $table->index('rappel');
            $table->index(['date_planifiee', 'terminee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};