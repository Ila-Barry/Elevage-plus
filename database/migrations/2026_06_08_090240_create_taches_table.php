<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animaux')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne');
            $table->enum('statut', ['en_attente', 'terminee', 'annulee'])->default('en_attente');
            $table->date('date_planifiee');
            $table->date('date_realisation')->nullable();
            $table->boolean('terminee')->default(false);
            $table->timestamps();
            
            // Index
            $table->index(['animal_id', 'statut']);
            $table->index(['date_planifiee']);
            $table->index(['priorite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};