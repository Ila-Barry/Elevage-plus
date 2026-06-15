<?php
// database/migrations/XXXX_XX_XX_XXXXXX_create_animal_historiques_table.php

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
        Schema::create('animal_historiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')
                ->nullable()  // Permettre NULL
                ->constrained('animaux')
                ->onDelete('set null');  // Au lieu de cascade
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('champ_modifie', 100);
            $table->text('ancienne_valeur')->nullable();
            $table->text('nouvelle_valeur')->nullable();
            $table->string('action', 50); // create, update, delete
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Index pour les performances
            $table->index('animal_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_historiques');
    }
};