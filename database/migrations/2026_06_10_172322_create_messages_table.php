<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->unsignedBigInteger('expediteur_id');
            $table->foreign('expediteur_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('destinataire_id');
            $table->foreign('destinataire_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('type', ['text', 'image', 'video', 'audio', 'file', 'sticker'])->default('text');
            $table->text('contenu')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable();
            $table->boolean('lu')->default(false);
            $table->timestamp('lu_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};