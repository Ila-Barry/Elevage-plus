<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('telephone')->unique();
        $table->text('bio')->nullable();
        $table->string('photo_url')->nullable();
        $table->enum('role', ['admin', 'user'])->default('user');
        $table->enum('status', ['active', 'inactive', 'bannie'])->default('inactive');
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};