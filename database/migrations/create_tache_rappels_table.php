<?php
// database/migrations/xxxx_xx_xx_create_tache_rappels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tache_rappels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tache_id')->constrained()->onDelete('cascade');
            $table->string('type_rappel', 20); // 72h, 48h, 24h, 1h, 30min, now, retard
            $table->timestamp('heure_envoi_prevue');
            $table->enum('statut', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('date_envoi')->nullable();
            $table->text('erreur_message')->nullable();
            $table->timestamps();
            
            $table->index(['tache_id', 'statut']);
            $table->index('heure_envoi_prevue');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tache_rappels');
    }
};