<?php
// table migration pour les partages des publications

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
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plateforme', 50); // facebook, twitter, whatsapp, copie_lien
            $table->timestamps();
            
            // Index pour les performances
            $table->index('publication_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};