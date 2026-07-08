<?php
// database/migrations/YYYY_MM_DD_HHMMSS_update_users_status_default.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Mettre à jour les statuts existants
        DB::table('users')->whereNull('status')->update(['status' => 'active']);
        
        // ✅ Modifier la valeur par défaut via SQL
        DB::statement("ALTER TABLE users MODIFY status ENUM('active', 'inactive', 'bannie') DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY status ENUM('active', 'inactive', 'bannie') DEFAULT NULL");
    }
};