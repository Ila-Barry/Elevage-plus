<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('elevage_id');
            $table->foreign('elevage_id')->references('id')->on('elevages')->onDelete('cascade');
            $table->string('nom');
            $table->decimal('quantite', 10, 2);
            $table->string('categorie');
            $table->decimal('seuil_alerte', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');  // ← ajouté
    }
};