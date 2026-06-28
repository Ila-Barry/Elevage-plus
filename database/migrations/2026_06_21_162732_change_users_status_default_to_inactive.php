<?php
// database/migrations/2025_01_01_000011_change_users_status_default_to_inactive.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modifier la colonne status pour ajouter 'inactive' et changer la valeur par défaut
            $table->enum('status', ['active', 'inactive', 'bannie'])->default('inactive')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'bannie'])->default('active')->change();
        });
    }
};