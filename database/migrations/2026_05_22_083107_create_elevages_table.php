<?php
// database/migrations/2025_01_01_000002_create_elevages_table.php

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
        Schema::create('elevages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nom', 100);
            $table->string('img_url')->nullable();
            $table->string('localisation', 200);
            $table->decimal('superficie', 10, 2)->default(0);
            $table->string('type_elevage', 50);
            $table->text('description')->nullable();
            $table->string('adresse', 200)->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('code_postal', 20)->nullable();
            $table->string('pays', 100)->default('Sénégal');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email_contact', 100)->nullable();
            $table->enum('statut', ['actif', 'inactif', 'ferme'])->default('actif');
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamps();
            
            // Index pour les performances
            $table->index('user_id');
            $table->index('type_elevage');
            $table->index('statut');
            $table->index('localisation');
            $table->index('ville');
            $table->index('pays');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevages');
    }
};