<?php
// database/migrations/2025_01_01_000003_create_publications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('titre', 200);
            $table->enum('categorie', ['experience', 'conseil', 'alerte'])->default('experience');
            $table->text('contenu');
            
            // ✅ Stockage JSON pour les médias multiples
            
            $table->json('images')->nullable();
            $table->json('videos')->nullable();
            $table->json('documents')->nullable();
            
            $table->integer('nbr_likes')->default(0);
            $table->integer('nbr_commentaires')->default(0);
            $table->integer('nbr_partages')->default(0);
            $table->integer('nbr_vues')->default(0);
            $table->integer('nbr_signalements')->default(0);
            $table->enum('statut', ['publiee', 'signalee', 'bloquee'])->default('publiee');
            $table->text('raison_blocage')->nullable();
            $table->timestamp('published_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['categorie', 'statut']);
            $table->index('published_at');
            $table->index('user_id');
            $table->index('nbr_likes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};