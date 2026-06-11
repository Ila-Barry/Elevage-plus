<?php
// database/migrations/2026_01_15_000001_add_conversation_id_to_messages.php

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
        // Ajouter la colonne conversation_id à la table messages
        Schema::table('messages', function (Blueprint $table) {
            // Vérifier si la colonne n'existe pas déjà
            if (!Schema::hasColumn('messages', 'conversation_id')) {
                $table->unsignedBigInteger('conversation_id')->after('id');
                $table->foreign('conversation_id')
                    ->references('id')
                    ->on('conversations')
                    ->onDelete('cascade');
            }
            
            // Ajouter la colonne lu_at si elle n'existe pas
            if (!Schema::hasColumn('messages', 'lu_at')) {
                $table->timestamp('lu_at')->nullable()->after('lu');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'conversation_id')) {
                $table->dropForeign(['conversation_id']);
                $table->dropColumn('conversation_id');
            }
            
            if (Schema::hasColumn('messages', 'lu_at')) {
                $table->dropColumn('lu_at');
            }
        });
    }
};