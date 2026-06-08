<?php
// database/migrations/2025_01_01_000003_create_animaux_table.php

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
        Schema::create('animaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elevage_id')->constrained()->onDelete('cascade');
            $table->string('nom', 100);
            $table->string('espece', 50);
            $table->string('race', 100)->nullable();
            $table->date('date_naissance');
            $table->decimal('poids', 8, 2)->default(0);
            $table->enum('statut_sanitaire', ['bon', 'a_surveiller', 'malade', 'critique'])->default('bon');
            $table->string('img_url')->nullable();
            $table->string('numero_identification', 50)->nullable()->unique();
            $table->enum('sexe', ['male', 'femelle'])->default('male');
            $table->string('couleur', 50)->nullable();
            $table->text('signes_particuliers')->nullable();
            $table->enum('statut', ['actif', 'vendu', 'decede', 'transfere'])->default('actif');
            $table->date('date_deces')->nullable();
            $table->text('motif_deces')->nullable();
            $table->unsignedBigInteger('pere_id')->nullable();
            $table->unsignedBigInteger('mere_id')->nullable();
            $table->timestamps();
            
            // Index pour les performances
            $table->index('elevage_id');
            $table->index('espece');
            $table->index('statut_sanitaire');
            $table->index('statut');
            $table->index('date_naissance');
            $table->index('numero_identification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animaux');
    }
};