<?php
// database/migrations/2026_01_15_000002_add_media_columns_to_messages_table.php

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
        Schema::table('messages', function (Blueprint $table) {
            // Ajouter les colonnes pour les médias
            if (!Schema::hasColumn('messages', 'type')) {
                $table->enum('type', ['text', 'image', 'video', 'file', 'sticker'])->default('text')->after('contenu');
            }
            
            if (!Schema::hasColumn('messages', 'media_url')) {
                $table->string('media_url')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('messages', 'media_type')) {
                $table->string('media_type')->nullable()->after('media_url');
            }
            
            if (!Schema::hasColumn('messages', 'media_size')) {
                $table->bigInteger('media_size')->nullable()->after('media_type');
            }
            
            if (!Schema::hasColumn('messages', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable()->after('media_size');
            }
            
            if (!Schema::hasColumn('messages', 'file_name')) {
                $table->string('file_name')->nullable()->after('thumbnail_url');
            }
            
            if (!Schema::hasColumn('messages', 'duration')) {
                $table->integer('duration')->nullable()->comment('Durée en secondes pour les vidéos')->after('file_name');
            }
            
            if (!Schema::hasColumn('messages', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false)->after('lu_at');
            }
            
            if (!Schema::hasColumn('messages', 'deleted_for_everyone')) {
                $table->boolean('deleted_for_everyone')->default(false)->after('is_deleted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $columns = ['type', 'media_url', 'media_type', 'media_size', 'thumbnail_url', 'file_name', 'duration', 'is_deleted', 'deleted_for_everyone'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};