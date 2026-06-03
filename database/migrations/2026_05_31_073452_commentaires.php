<?php
// database/migrations/2025_01_01_000004_create_commentaires_table.php

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
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('commentaires')->onDelete('cascade');
            $table->text('contenu');
            $table->integer('nbr_likes')->default(0);
            $table->boolean('is_edited')->default(false);
            $table->timestamps();
            
            // Index pour les performances
            $table->index('publication_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};