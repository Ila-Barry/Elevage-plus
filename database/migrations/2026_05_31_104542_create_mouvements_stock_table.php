<?php
// database/migrations/2025_01_01_000009_create_mouvements_stock_table.php

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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('elevage_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['entree', 'sortie']);
            $table->decimal('quantite', 12, 2);
            $table->decimal('quantite_avant', 12, 2);
            $table->decimal('quantite_apres', 12, 2);
            $table->string('motif', 100);
            $table->text('description')->nullable();
            $table->string('reference_facture')->nullable();
            $table->string('fournisseur')->nullable();
            $table->string('destinataire')->nullable();
            $table->timestamp('date_mouvement')->useCurrent();
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['produit_id', 'type']);
            $table->index('date_mouvement');
            $table->index('motif');
            $table->index(['elevage_id', 'date_mouvement']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};