<?php
// database/migrations/YYYY_MM_DD_HHMMSS_add_json_media_columns_to_publications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            // ✅ Ajouter les colonnes JSON
            $table->json('images')->nullable()->after('contenu');
            $table->json('videos')->nullable()->after('images');
            $table->json('documents')->nullable()->after('videos');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['images', 'videos', 'documents']);
        });
    }
};