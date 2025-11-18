<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Dodaje pole product_description_id do tabeli api_usage_logs.
     */
    public function up(): void
    {
        Schema::table('api_usage_logs', function (Blueprint $table) {
            $table->foreignId('product_description_id')
                ->nullable()
                ->after('api_key_id')
                ->constrained()
                ->onDelete('set null');
            
            $table->index('product_description_id');
        });
    }

    /**
     * Reverse the migrations.
     * Usuwa pole product_description_id z tabeli api_usage_logs.
     */
    public function down(): void
    {
        Schema::table('api_usage_logs', function (Blueprint $table) {
            $table->dropForeign(['product_description_id']);
            $table->dropIndex(['product_description_id']);
            $table->dropColumn('product_description_id');
        });
    }
};
