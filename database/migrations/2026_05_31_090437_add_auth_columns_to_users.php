<?php
// database/migrations/2025_01_01_000002_add_auth_columns_to_users_table.php

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
        Schema::table('users', function (Blueprint $table) {
            // Ajout des colonnes pour les préférences et sécurité
            $table->enum('profile_visibility', ['public', 'prive'])->default('public')->after('photo_url');
            $table->boolean('email_notifications')->default(true)->after('profile_visibility');
            $table->boolean('web_notifications')->default(true)->after('email_notifications');
            $table->boolean('reminder_notifications')->default(true)->after('web_notifications');
            $table->boolean('newsletter_subscription')->default(false)->after('reminder_notifications');
            $table->boolean('two_factor_enabled')->default(false)->after('newsletter_subscription');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_visibility',
                'email_notifications',
                'web_notifications',
                'reminder_notifications',
                'newsletter_subscription',
                'two_factor_enabled',
                'two_factor_secret',
            ]);
        });
    }
};