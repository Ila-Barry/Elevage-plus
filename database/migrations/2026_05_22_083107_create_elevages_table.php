<?php
// database/migrations/2024_01_01_000001_create_elevages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Crée la table elevages conforme au cahier des charges :
     * - Un éleveur peut avoir plusieurs élevages
     * - Suppression en cascade (animaux + tâches associées)
     */
    public function up(): void
    {
        Schema::create('elevages', function (Blueprint $table) {
            $table->id();
            
            // Relation avec l'utilisateur (éleveur)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade'); // Suppression cascade conforme ELEV-03
            
            // Informations de base
            $table->string('nom');
            $table->string('localisation');
            $table->integer('superficie')->default(0);
            $table->string('type_elevage'); // bovins, ovins, caprins, volailles, mixte
            
            // Options supplémentaires
            $table->string('img_url')->nullable();
            $table->text('description')->nullable();
            
            // Timestamps pour suivi
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['user_id', 'type_elevage']);
            $table->index('nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevages');
    }
};
?>