<?php
// database/migrations/2024_01_01_000003_create_animal_historiques_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Crée la table animal_historiques pour suivre toutes les modifications
     * Conforme à ANIM-02 : Historique des modifications optionnel
     */
    public function up(): void
    {
        Schema::create('animal_historiques', function (Blueprint $table) {
            $table->id();
            
            // Relation avec l'animal
            $table->unsignedBigInteger('animal_id');
            $table->foreign('animal_id')
                  ->references('id')
                  ->on('animals')
                  ->onDelete('cascade');
            
            // Utilisateur qui a fait la modification
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Type d'action
            $table->enum('action', ['create', 'update', 'delete', 'restore']);
            
            // Données avant modification (JSON)
            $table->json('before_data')->nullable();
            
            // Données après modification (JSON)
            $table->json('after_data')->nullable();
            
            // Champs modifiés
            $table->json('changed_fields')->nullable();
            
            // IP et user agent pour traçabilité
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Index pour recherche rapide
            $table->index(['animal_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_historiques');
    }
};