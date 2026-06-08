<?php
// database/migrations/2025_01_01_000010_add_stock_columns_to_produits_table.php

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
        Schema::table('produits', function (Blueprint $table) {
            $table->decimal('prix_unitaire', 12, 2)->nullable()->after('quantite');
            $table->decimal('prix_total', 12, 2)->nullable()->after('prix_unitaire');
            $table->date('date_expiration')->nullable()->after('description');
            $table->string('emplacement_stockage')->nullable()->after('date_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn([
                'prix_unitaire',
                'prix_total',
                'date_expiration',
                'emplacement_stockage',
            ]);
        });
    }
};