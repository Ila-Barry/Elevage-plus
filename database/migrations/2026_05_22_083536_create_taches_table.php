<?php
// database/migrations/2024_01_01_000004_create_taches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Crée la table taches conforme au cahier des charges :
     * - Type (vaccination/pesée/vermifuge/soin)
     * - Peut être liée à un animal ou à l'élevage entier
     * - Date planifiée et date réalisée
     * - Statut terminée
     */
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            
            // Relation avec l'animal (peut être null pour tâche élevage entier)
            $table->unsignedBigInteger('animal_id')->nullable();
            $table->foreign('animal_id')
                  ->references('id')
                  ->on('animals')
                  ->onDelete('cascade');
            
            // Relation avec l'élevage (nécessaire pour les tâches d'élevage entier)
            $table->unsignedBigInteger('elevage_id');
            $table->foreign('elevage_id')
                  ->references('id')
                  ->on('elevages')
                  ->onDelete('cascade');
            
            // Informations de la tâche
            $table->string('titre');
            $table->enum('type', [
                'vaccination',
                'pesee',
                'vermifuge',
                'soin',
                'alimentation',
                'nettoyage',
                'autre'
            ]);
            
            // Dates
            $table->date('date_planifiee');
            $table->date('date_realisee')->nullable();
            
            // Statut
            $table->boolean('terminee')->default(false);
            
            // Détails supplémentaires
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['elevage_id', 'date_planifiee']);
            $table->index(['animal_id', 'terminee']);
            $table->index('date_planifiee');
            $table->index('type');
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