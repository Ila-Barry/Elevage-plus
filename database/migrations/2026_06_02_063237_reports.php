<?php
// Table pour les signalements des publications (reports)
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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('motif', ['spam', 'offensant', 'fausse_info', 'contenu_inapproprie', 'autre'])->default('autre');
            $table->text('commentaire')->nullable();
            $table->enum('statut', ['en_attente', 'traite', 'ignore'])->default('en_attente');
            $table->timestamps();
            
            // Un utilisateur ne peut signaler qu'une fois la même publication
            $table->unique(['publication_id', 'user_id']);
            
            // Index pour les performances
            $table->index('publication_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};