<?php
// database/migrations/xxxx_xx_xx_add_notification_preferences_to_users.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'web_notifications')) {
                $table->boolean('web_notifications')->default(true)->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(false)->after('web_notifications');
            }
            if (!Schema::hasColumn('users', 'message_notifications')) {
                $table->boolean('message_notifications')->default(true)->after('email_notifications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['web_notifications', 'email_notifications', 'message_notifications']);
        });
    }
};