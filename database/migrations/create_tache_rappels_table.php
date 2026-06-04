<?php
// database/migrations/2024_01_01_000005_create_tache_rappels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Crée la table tache_rappels pour gérer les rappels automatiques
     * Conforme au cahier des charges : rappels à 48h, 24h, 1h, 30min
     */
    public function up(): void
    {
        Schema::create('tache_rappels', function (Blueprint $table) {
            $table->id();
            
            // Relation avec la tâche
            $table->unsignedBigInteger('tache_id');
            $table->foreign('tache_id')
                  ->references('id')
                  ->on('taches')
                  ->onDelete('cascade');
            
            // Type de rappel
            $table->enum('type_rappel', [
                '48h',   // 48 heures avant
                '24h',   // 24 heures avant
                '1h',    // 1 heure avant
                '30min', // 30 minutes avant
                'now'    // À l'heure
            ]);
            
            // Heure prévue d'envoi
            $table->datetime('heure_envoi_prevue');
            
            // Statut d'envoi
            $table->enum('statut', ['pending', 'sent', 'failed'])->default('pending');
            
            // Date d'envoi réel
            $table->datetime('date_envoi')->nullable();
            
            // Message d'erreur si échec
            $table->text('erreur_message')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['statut', 'heure_envoi_prevue']);
            $table->index('tache_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tache_rappels');
    }
};