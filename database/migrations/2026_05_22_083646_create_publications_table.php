<?php
// database/migrations/2025_01_01_000003_create_publications_table.php

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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('titre', 200);
            $table->enum('categorie', ['experience', 'conseil', 'alerte'])->default('experience');
            $table->text('contenu'); // 1 million de caractères max (MEDIUMTEXT)
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('fichier_url')->nullable();
            $table->string('fichier_nom')->nullable();
            $table->integer('nbr_likes')->default(0);
            $table->integer('nbr_commentaires')->default(0);
            $table->integer('nbr_partages')->default(0);
            $table->integer('nbr_vues')->default(0);
            $table->integer('nbr_signalements')->default(0);
            $table->enum('statut', ['publiee', 'signalee', 'bloquee'])->default('publiee');
            $table->text('raison_blocage')->nullable();
            $table->timestamp('published_at')->useCurrent();
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['categorie', 'statut']);
            $table->index('published_at');
            $table->index('user_id');
            $table->index('nbr_likes');
            $table->index('nbr_vues');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};