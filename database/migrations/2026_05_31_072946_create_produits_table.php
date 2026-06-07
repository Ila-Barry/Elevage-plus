<?php
// database/migrations/2025_01_01_000008_create_produits_table.php

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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elevage_id')->constrained()->onDelete('cascade');
            $table->string('nom', 100);
            $table->enum('categorie', [
                'aliment',
                'medicament', 
                'equipement',
                'vaccin',
                'accessoire',
                'autre'
            ])->default('aliment');
            $table->decimal('quantite', 12, 2)->default(0);
            $table->decimal('seuil_alerte', 12, 2)->default(0);
            $table->string('unite', 20)->default('unite'); // kg, litre, piece, boite, etc.
            $table->string('fournisseur')->nullable();
            $table->text('description')->nullable();
            $table->string('code_barre')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('statut', ['actif', 'inactif', 'rupture'])->default('actif');
            $table->timestamp('derniere_commande')->nullable();
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['elevage_id', 'categorie']);
            $table->index('nom');
            $table->index('seuil_alerte');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};