<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Dodaje pole external_product_id dla asynchronicznego generowania.
     * Adds external_product_id field for async generation.
     */
    public function up(): void
    {
        Schema::table('product_descriptions', function (Blueprint $table) {
            // Zewnetrzny ID produktu od klienta dla latwiejszej identyfikacji
            $table->string('external_product_id', 255)
                ->nullable()
                ->after('request_id')
                ->index()
                ->comment('External product ID from client for easier identification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_descriptions', function (Blueprint $table) {
            $table->dropColumn('external_product_id');
        });
    }
};
