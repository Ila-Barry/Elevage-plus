<?php
// database/migrations/2024_01_01_000002_create_animals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Crée la table animals conforme au cahier des charges :
     * - Suivi individuel (espèce, race, âge, poids, santé)
     * - Relation avec élevage (suppression cascade)
     * - Calcul automatique de l'âge via date_naissance
     */
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            
            // Relation avec l'élevage (suppression cascade conforme ANIM-03)
            $table->unsignedBigInteger('elevage_id');
            $table->foreign('elevage_id')
                  ->references('id')
                  ->on('elevages')
                  ->onDelete('cascade');
            
            // Informations de base
            $table->string('nom');
            $table->string('race');
            $table->string('espece'); // bovin, ovin, caprin, volaille
            $table->decimal('poids', 10, 2); // poids en kg
            
            // Suivi sanitaire
            $table->string('statut_sanitaire'); // sain, sous_traitement, en_quarantaine, malade
            
            // Médias et description
            $table->string('img_url')->nullable();
            $table->text('description')->nullable();
            
            // Pour le calcul automatique de l'âge
            $table->date('date_naissance');
            
            // Timestamps
            $table->timestamps();
            
            // Index pour optimiser les requêtes de filtrage
            $table->index(['elevage_id', 'espece']);
            $table->index(['statut_sanitaire', 'date_naissance']);
            $table->index('nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};